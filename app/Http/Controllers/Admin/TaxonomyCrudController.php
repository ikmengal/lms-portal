<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Course;

abstract class TaxonomyCrudController extends Controller
{
    abstract protected function model(): string;

    abstract protected function label(): string;

    abstract protected function singular(): string;

    abstract protected function fkColumn(): string;

    abstract protected function routePrefix(): string;

    public function index(Request $request)
    {
        $model = $this->model();

        $items = $model::query()
            ->when($request->filled('search'), fn ($q) => $q->where('name', 'like', '%' . $request->search . '%'))
            ->ordered()
            ->get();

        return view('pages.admin.taxonomy.index', [
            'items' => $items,
            'trashed' => $model::onlyTrashed()->orderBy('deleted_at')->get(),
            'label' => $this->label(),
            'singular' => $this->singular(),
            'baseRoute' => $this->routePrefix(),
            'usageCounts' => $this->usageCounts(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $model = $this->model();

        $model::create($data + [
            'slug' => Str::slug($data['name']),
            'sort_order' => (int) $model::max('sort_order') + 1,
        ]);

        return back()->with('success', "{$this->singular()} \"{$data['name']}\" added.");
    }

    public function update(Request $request, int $id)
    {
        $item = $this->findOrFail($id);
        $data = $this->validated($request, $id);

        $item->update([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
        ]);

        return back()->with('success', "{$this->singular()} updated.");
    }

    public function move(Request $request, int $id)
    {
        $item = $this->findOrFail($id);
        $model = $this->model();

        $swap = $model::ordered()
            ->where('sort_order', $request->direction === 'up' ? '<' : '>')
            ->when($request->direction === 'up', fn ($q) => $q->latest('sort_order'))
            ->first();

        if ($swap) {
            [$item->sort_order, $swap->sort_order] = [$swap->sort_order, $item->sort_order];
            $item->save();
            $swap->save();
        }

        return back();
    }

    public function toggleActive(int $id)
    {
        $item = $this->findOrFail($id);
        $item->update(['is_active' => ! $item->is_active]);

        return back()->with('success', "{$this->singular()} {$item->name} is now " . ($item->is_active ? 'active' : 'hidden') . '.');
    }

    public function destroy(int $id)
    {
        $item = $this->findOrFail($id);
        $inUse = $this->usageCounts()[$item->id] ?? 0;

        $item->delete();

        return back()->with('success', $inUse > 0
            ? "{$this->singular()} \"{$item->name}\" moved to trash. Courses using it are unaffected."
            : "{$this->singular()} \"{$item->name}\" deleted.");
    }

    public function restore(int $id)
    {
        $model = $this->model();
        $item = $model::onlyTrashed()->findOrFail($id);
        $item->restore();

        return back()->with('success', "{$this->singular()} \"{$item->name}\" restored.");
    }

    public function forceDelete(int $id)
    {
        $model = $this->model();
        $item = $model::onlyTrashed()->findOrFail($id);
        $item->forceDelete();

        return back()->with('success', "{$this->singular()} \"{$item->name}\" permanently deleted.");
    }

    private function findOrFail(int $id): Model
    {
        $model = $this->model();

        return $model::findOrFail($id);
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        $table = (new ($this->model()))->getTable();

        return $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique($table, 'name')->whereNull('deleted_at')->ignore($ignoreId),
            ],
        ]);
    }

    private function usageCounts(): array
    {
        $fk = $this->fkColumn();

        return Course::withTrashed()
            ->selectRaw("$fk as term_id, COUNT(*) as total")
            ->whereNotNull($fk)
            ->groupBy($fk)
            ->pluck('total', 'term_id')
            ->all();
    }
}
