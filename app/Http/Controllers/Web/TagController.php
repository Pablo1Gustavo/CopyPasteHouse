<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TagController extends Controller
{
    /**
     * Display a listing of tags (admin view)
     */
    public function index()
    {
        $tags = Tag::with('user')
            ->withCount('pastes')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.tags.index', compact('tags'));
    }

    /**
     * Show the form for creating a new tag
     */
    public function create()
    {
        return view('admin.tags.create');
    }

    /**
     * Store a newly created tag
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:tags,name',
            'description' => 'nullable|string|max:500',
            'color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'is_public' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['user_id'] = auth()->id();
        
        // Only admins can create public tags; regular users' tags are always private
        if (auth()->user()->is_admin) {
            $validated['is_public'] = $validated['is_public'] ?? true;
        } else {
            $validated['is_public'] = false;
        }

        Tag::create($validated);

        // Redirect to My Tags for regular users, admin index for admins
        $route = auth()->user()->is_admin ? 'tags.index' : 'tags.my';
        return redirect()->route($route)
            ->with('success', 'Tag created successfully');
    }

    /**
     * Display the specified tag
     */
    public function show(string $id)
    {
        $tag = Tag::withCount('pastes')->find($id);

        if (!$tag) {
            return redirect()->route('tags.index')
                ->with('error', 'Tag not found');
        }

        $pastes = $tag->pastes()
            ->with(['user', 'syntaxHighlight'])
            ->withCount(['likes', 'comments'])
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.tags.show', compact('tag', 'pastes'));
    }

    /**
     * Show the form for editing the specified tag
     */
    public function edit(string $id)
    {
        $tag = Tag::find($id);

        if (!$tag) {
            return redirect()->route('tags.index')
                ->with('error', 'Tag not found');
        }

        // Check permission: only admin or tag owner can edit
        if (!auth()->user()->is_admin && auth()->id() !== $tag->user_id) {
            return redirect()->route('tags.index')
                ->with('error', 'You do not have permission to edit this tag');
        }

        return view('admin.tags.edit', compact('tag'));
    }

    /**
     * Update the specified tag
     */
    public function update(Request $request, string $id)
    {
        $tag = Tag::find($id);

        if (!$tag) {
            return redirect()->route('tags.index')
                ->with('error', 'Tag not found');
        }

        // Check permission: only admin or tag owner can update
        if (!auth()->user()->is_admin && auth()->id() !== $tag->user_id) {
            return redirect()->route('tags.index')
                ->with('error', 'You do not have permission to edit this tag');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:tags,name,' . $id,
            'description' => 'nullable|string|max:500',
            'color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'is_public' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        
        // Only admins can change the public status
        if (auth()->user()->is_admin) {
            $validated['is_public'] = $validated['is_public'] ?? $tag->is_public;
        } else {
            // Regular users cannot make their tags public
            $validated['is_public'] = false;
        }

        $tag->update($validated);

        // Redirect to My Tags for regular users, admin index for admins
        $route = auth()->user()->is_admin ? 'tags.index' : 'tags.my';
        return redirect()->route($route)
            ->with('success', 'Tag updated successfully');
    }

    /**
     * Remove the specified tag
     */
    public function destroy(string $id)
    {
        $tag = Tag::find($id);

        if (!$tag) {
            $route = auth()->user()->is_admin ? 'tags.index' : 'tags.my';
            return redirect()->route($route)
                ->with('error', 'Tag not found');
        }

        // Check permission: only admin or tag owner can delete
        if (!auth()->user()->is_admin && auth()->id() !== $tag->user_id) {
            $route = auth()->user()->is_admin ? 'tags.index' : 'tags.my';
            return redirect()->route($route)
                ->with('error', 'You do not have permission to delete this tag');
        }

        $tag->delete();

        // Redirect to My Tags for regular users, admin index for admins
        $route = auth()->user()->is_admin ? 'tags.index' : 'tags.my';
        return redirect()->route($route)
            ->with('success', 'Tag deleted successfully');
    }

    /**
     * Display public tag cloud/list
     */
    public function publicIndex()
    {
        $tags = Tag::whereHas('pastes')
            ->withCount('pastes')
            ->orderByDesc('pastes_count')
            ->get();

        return view('tags.index', compact('tags'));
    }

    /**
     * Display user's own tags
     */
    public function myTags()
    {
        $tags = Tag::where('user_id', auth()->id())
            ->withCount('pastes')
            ->orderBy('name')
            ->paginate(20);

        return view('tags.my-tags', compact('tags'));
    }

    /**
     * Display pastes with a specific tag (public view)
     */
    public function publicShow(string $slug)
    {
        $tag = Tag::where('slug', $slug)->where('is_public', true)->withCount('pastes')->first();

        if (!$tag) {
            return redirect()->route('tags.public')
                ->with('error', 'Tag not found');
        }

        $pastes = $tag->pastes()
            ->where('listable', true)
            ->whereNull('password')
            ->where(function ($query) {
                $query->whereNull('expiration')
                      ->orWhere('expiration', '>', now());
            })
            ->with(['user', 'syntaxHighlight'])
            ->withCount(['likes', 'comments'])
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('tags.show', compact('tag', 'pastes'));
    }
}
