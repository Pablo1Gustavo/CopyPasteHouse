<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePasteRequest;
use App\Http\Requests\UpdatePasteRequest;
use App\Models\ExpirationTime;
use App\Models\Paste;
use App\Models\SyntaxHighlight;
use App\Services\PasteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PasteController extends Controller
{
    public function __construct(
        private PasteService $pasteService
    ) {}


    public function create()
    {
        $syntaxHighlights = SyntaxHighlight::all();
        $expirationTimes = ExpirationTime::all();
        
        return view('dashboard', compact('syntaxHighlights', 'expirationTimes'));
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

        if (isset($data['tags']) && is_string($data['tags'])) {
            $tags = array_map('trim', explode(',', $data['tags']));
            $data['tags'] = array_values(array_filter($tags, fn ($tag) => $tag !== ''));
        }
        
        $paste = $this->pasteService->create($data, Auth::user());
        
        return redirect()->route('pastes.show', $paste->id)
            ->with('success', 'Paste created successfully!');
    }


    public function show(string $id, Request $request)
    {
        $paste = Paste::with(['syntaxHighlight', 'user'])
            ->withCount(['likes', 'accessLogs as access_count'])
            ->find($id);
        
        if (!$paste) {
            abort(404, 'Paste not found');
        }
        
        // Check if paste is expired
        if ($paste->expiration && $paste->expiration->isPast()) {
            abort(404, 'Paste has expired');
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

        $paste->accessLogs()->create([
            'user_id'     => Auth::id(),
            'ip'          => $request->ip(),
            'user_agent'  => $request->userAgent(),
            'access_date' => now(),
        ]);

        $accessCount = ($paste->access_count ?? 0) + 1;

        if ($paste->destroy_on_open) {
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

            $paste->delete();

            return view('pastes.show', ['paste' => $displayPaste]);
        }

        $paste->setAttribute('access_count', $accessCount);
        $paste->makeHidden('password');

        return view('pastes.show', compact('paste'));
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
        
        $syntaxHighlights = SyntaxHighlight::all();
        $expirationTimes = ExpirationTime::all();
        
        return view('pastes.edit', compact('paste', 'syntaxHighlights', 'expirationTimes'));
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
        
        if (isset($data['tags']) && is_string($data['tags'])) {
            $tags = array_map('trim', explode(',', $data['tags']));
            $data['tags'] = array_values(array_filter($tags, fn ($tag) => $tag !== ''));
        }
        
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        
        $paste = $this->pasteService->edit($paste, $data);
        
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
}
