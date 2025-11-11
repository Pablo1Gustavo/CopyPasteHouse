<?php declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\SyntaxHighlight;
use App\Services\SyntaxHighlightService;
use Illuminate\Http\Request;

class SyntaxHighlightController extends Controller
{
    public function __construct(
        private SyntaxHighlightService $syntaxHighlightService
    ) {
    }

    /**
     * Display a listing of syntax highlights
     */
    public function index()
    {
        $highlights = $this->syntaxHighlightService->list();
        return view('admin.syntax-highlights.index', compact('highlights'));
    }

    /**
     * Show the form for creating a new syntax highlight
     */
    public function create()
    {
        return view('admin.syntax-highlights.create');
    }

    /**
     * Store a newly created syntax highlight
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:syntax_highlights,name',
            'extension' => 'required|string|max:50|unique:syntax_highlights,extension',
        ]);

        $this->syntaxHighlightService->create($validated);

        return redirect()->route('syntax-highlights.index')
            ->with('success', 'Syntax highlight created successfully');
    }

    /**
     * Show the form for editing the specified syntax highlight
     */
    public function edit(string $syntax_highlight)
    {
        $highlight = $this->syntaxHighlightService->show($syntax_highlight);

        if (!$highlight)
        {
            return redirect()->route('syntax-highlights.index')
                ->with('error', 'Syntax highlight not found');
        }

        return view('admin.syntax-highlights.edit', compact('highlight'));
    }

    /**
     * Update the specified syntax highlight
     */
    public function update(Request $request, string $syntax_highlight)
    {
        $highlight = $this->syntaxHighlightService->show($syntax_highlight);

        if (!$highlight)
        {
            return redirect()->route('syntax-highlights.index')
                ->with('error', 'Syntax highlight not found');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:syntax_highlights,name,' . $syntax_highlight,
            'extension' => 'required|string|max:50|unique:syntax_highlights,extension,' . $syntax_highlight,
        ]);

        $this->syntaxHighlightService->edit($highlight, $validated);

        return redirect()->route('syntax-highlights.index')
            ->with('success', 'Syntax highlight updated successfully');
    }

    /**
     * Remove the specified syntax highlight
     */
    public function destroy(SyntaxHighlight $syntaxHighlight)
    {
        $result = $this->syntaxHighlightService->delete($syntaxHighlight);

        if (!$result)
        {
            return redirect()->route('syntax-highlights.index')
                ->with('error', 'Syntax highlight not found or cannot be deleted');
        }

        return redirect()->route('syntax-highlights.index')
            ->with('success', 'Syntax highlight deleted successfully');
    }
}
