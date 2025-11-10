<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePasteRequest;
use App\Http\Requests\StorePasteCommentRequest;
use App\Http\Requests\UpdatePasteRequest;
use App\Models\Paste;
use App\Models\PasteComment;
use App\Models\PasteLike;
use App\Models\PasteAccessLog;
use App\Services\ExpirationTimeService;
use App\Services\PasteCommentService;
use App\Services\PasteService;
use App\Services\SyntaxHighlightService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PasteController extends Controller
{
    public function __construct(
        private PasteService $pasteService,
        private PasteCommentService $pasteCommentService,
        private SyntaxHighlightService $syntaxHighlightService,
        private ExpirationTimeService $expirationTimeService
    ) {}


    public function create()
    {
        $syntaxHighlights = $this->syntaxHighlightService->list();
        $expirationTimes = $this->expirationTimeService->list();
        
        // Get public tags and user's own tags
        $tags = \App\Models\Tag::where(function($query) {
            $query->where('is_public', true);
            if (Auth::check()) {
                $query->orWhere('user_id', Auth::id());
            }
        })->orderBy('name')->get();
        
        return view('dashboard', compact('syntaxHighlights', 'expirationTimes', 'tags'));
    }


    public function store(StorePasteRequest $request)
    {
        $data = $request->validated();
        if (Auth::check()) {
            $data['user_id'] = Auth::id();
        }
        
        if (array_key_exists('expiration', $data)) {
            if ($data['expiration'] !== null && $data['expiration'] !== '') {
                $data['expiration'] = now()->addMinutes((int) $data['expiration']);
            } else {
                $data['expiration'] = null;
            }
        }

        // Extract tag_ids before creating paste
        $tagIds = $data['tag_ids'] ?? [];
        unset($data['tag_ids']);
        unset($data['tags']); // Remove old tags field if exists
        
        $paste = $this->pasteService->create($data, Auth::user());
        
        // Attach all tags to paste (remove duplicates)
        if (!empty($tagIds)) {
            $paste->tags()->attach(array_unique($tagIds));
        }
        
        return redirect()->route('pastes.show', ['id' => $paste->id, 'created' => '1'])
            ->with('success', 'Paste created successfully!');
    }


    public function show(string $id, Request $request)
    {
        $paste = Paste::with(['syntaxHighlight', 'user', 'tags'])
            ->withCount(['likes', 'accessLogs as access_count'])
            ->find($id);
        
        if (!$paste) {
            abort(404, 'Paste not found');
        }
        
        // Check if paste is expired
        if ($paste->expiration && $paste->expiration->isPast()) {
            abort(404, 'Paste has expired');
        }
        
        // Load comments with relationships and pagination
        $comments = $paste->comments()
            ->with(['user', 'syntaxHighlight'])
            ->withCount('likes')
            ->latest()
            ->paginate(20);
        
        // Check if current user has liked the paste and comments
        $userHasLiked = false;
        $likedCommentIds = [];
        if (Auth::check()) {
            $userHasLiked = $this->pasteService->isLikedByUser($paste, Auth::user());
            $likedCommentIds = $comments->filter(function($comment) {
                return $this->pasteCommentService->isLikedByUser($comment, Auth::user());
            })->pluck('id')->toArray();
        }
        
        // Make password visible to check if it exists
        $paste->makeVisible('password');

        $requiresPassword = !empty($paste->password);

        if ($requiresPassword) {
            $passwordProvided = $request->input('password');

            if (!$passwordProvided) {
                $paste->makeHidden('password');
                return view('pastes.password', compact('paste'));
            }

            if (!Hash::check($passwordProvided, $paste->password)) {
                $paste->makeHidden('password');
                return back()->withErrors(['password' => 'Invalid password']);
            }
        }

        // Check if this session has already viewed this paste
        $sessionKey = 'viewed_paste_' . $paste->id;
        $hasViewedInSession = session()->has($sessionKey);

        // Only increment view count if this is a new view for this session
        if (!$hasViewedInSession) {
            $paste->accessLogs()->create([
                'user_id'     => Auth::id(),
                'ip'          => $request->ip(),
                'user_agent'  => $request->userAgent(),
                'access_date' => now(),
            ]);

            // Mark this paste as viewed in the current session
            session()->put($sessionKey, true);
        }

        $accessCount = $paste->access_count ?? 0;
        if (!$hasViewedInSession) {
            $accessCount++;
        }

        // Check if this is the initial view after creation (skip destroy on open warning)
        $isInitialView = $request->query('created') === '1';
        
        // Check if the current viewer is the creator of the paste
        $isCreator = Auth::check() && Auth::id() === $paste->user_id;

        // If paste has destroy_on_open AND viewer is not the creator, handle burn logic
        if ($paste->destroy_on_open && !$isCreator && !$isInitialView && !$request->has('confirm_burn')) {
            $paste->setAttribute('access_count', $accessCount);
            $paste->makeHidden('password');
            return view('pastes.burn-warning', compact('paste'));
        }

        // If confirmed and not the creator, proceed with viewing/destruction
        if ($paste->destroy_on_open && !$isCreator && !$isInitialView && $request->has('confirm_burn')) {
            $displayPaste = (object) [
                'id' => null,
                'title' => $paste->title,
                'content' => $paste->content,
                'syntaxHighlight' => $paste->syntaxHighlight,
                'created_at' => $paste->created_at,
                'expiration' => $paste->expiration,
                'tags' => $paste->tags,
                'likes_count' => $paste->likes_count ?? 0,
                'access_count' => $accessCount,
                'user_id' => $paste->user_id,
                'destroyed' => true
            ];

            // Use the service method to properly handle cascading deletes
            $this->pasteService->delete($paste);

            return view('pastes.show', ['paste' => $displayPaste]);
        }

        $paste->setAttribute('access_count', $accessCount);
        $paste->makeHidden('password');

        $syntaxHighlights = $this->syntaxHighlightService->list();

        return view('pastes.show', compact(
            'paste', 
            'comments', 
            'userHasLiked', 
            'likedCommentIds', 
            'syntaxHighlights'
        ));
    }


    public function index()
    {
        $pastes = Paste::with(['syntaxHighlight', 'user'])
            ->withCount(['likes', 'comments'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('pastes.index', compact('pastes'));
    }


    public function edit(Paste $paste)
    {
        if ($paste->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }
        
        $syntaxHighlights = $this->syntaxHighlightService->list();
        $expirationTimes = $this->expirationTimeService->list();
        
        // Get public tags and user's own tags
        $tags = \App\Models\Tag::where(function($query) {
            $query->where('is_public', true)
                  ->orWhere('user_id', Auth::id());
        })->orderBy('name')->get();
        
        return view('pastes.edit', compact('paste', 'syntaxHighlights', 'expirationTimes', 'tags'));
    }


    public function update(UpdatePasteRequest $request, Paste $paste)
    {
        if ($paste->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }
        
        $data = $request->validated();
        
        if (array_key_exists('expiration', $data)) {
            if ($data['expiration'] !== null && $data['expiration'] !== '') {
                $data['expiration'] = now()->addMinutes((int) $data['expiration']);
            } else {
                $data['expiration'] = null;
            }
        }
        
        // Extract tag_ids before updating paste
        $tagIds = $data['tag_ids'] ?? [];
        unset($data['tag_ids']);
        
        // Remove old 'tags' field if it exists
        unset($data['tags']);
        
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        
        $paste = $this->pasteService->edit($paste, $data);
        
        // Sync tags (replaces all existing tags)
        $paste->tags()->sync(array_unique($tagIds));
        
        return redirect()->route('pastes.show', $paste->id)
            ->with('success', 'Paste updated successfully!');
    }


    public function destroy(Paste $paste)
    {
        if ($paste->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }
        
        $this->pasteService->delete($paste);
        
        return redirect()->route('pastes.index')
            ->with('success', 'Paste deleted successfully!');
    }


    public function toggleLike(Paste $paste)
    {
        $result = $this->pasteService->toggleLike($paste, Auth::user());
        
        return response()->json($result);
    }

    public function archive(Request $request)
    {
        $query = Paste::with(['syntaxHighlight', 'user', 'tags'])
            ->withCount(['likes', 'comments', 'accessLogs as access_count'])
            ->where('listable', true)
            ->whereNull('password')
            ->where(function ($q) {
                $q->whereNull('expiration')
                  ->orWhere('expiration', '>', now());
            });

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // Tag filter
        if ($request->filled('tag')) {
            $query->whereHas('tags', function ($q) use ($request) {
                $q->where('slug', $request->tag);
            });
        }

        // Syntax highlight filter
        if ($request->filled('syntax')) {
            $query->where('syntax_highlight_id', $request->syntax);
        }

        $pastes = $query->latest()->paginate(20)->withQueryString();

        // Get public tags for filter dropdown
        $tags = \App\Models\Tag::where('is_public', true)
            ->orderBy('name')
            ->get();
        $syntaxHighlights = $this->syntaxHighlightService->list();

        return view('pastes.archive', compact('pastes', 'tags', 'syntaxHighlights'));
    }

    public function raw(string $id)
    {
        $paste = Paste::find($id);
        
        if (!$paste) {
            abort(404, 'Paste not found');
        }
        
        // Check if paste is expired
        if ($paste->expiration && $paste->expiration->isPast()) {
            abort(404, 'Paste has expired');
        }
        
        return response($paste->content)
            ->header('Content-Type', 'text/plain; charset=UTF-8');
    }

    public function storeComment(StorePasteCommentRequest $request, Paste $paste)
    {
        $data = $request->validated();
        $this->pasteCommentService->create($paste, Auth::user(), $data);
        
        return redirect()->route('pastes.show', $paste->id)
            ->with('success', 'Comment posted successfully!');
    }

    public function toggleCommentLike(PasteComment $comment)
    {
        $result = $this->pasteCommentService->toggleLike($comment, Auth::user());
        
        return response()->json($result);
    }
}
