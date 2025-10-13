<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class ServiceController extends Controller
{
    /**
     * Display the list of available salon services.
     */
    public function index(Request $request): View
    {
        $storeId = (int) $request->integer('store_id', 1);

        $categories = ServiceCategory::query()
            ->select(['id', 'name'])
            ->whereHas('services', function ($query) use ($storeId): void {
                $query->where('store_id', $storeId);
            })
            ->with(['services' => function ($query) use ($storeId): void {
                $query->select([
                    'id',
                    'service_category_id',
                    'store_id',
                    'name',
                    'description',
                    'duration',
                    'price',
                ])
                ->where('store_id', $storeId)
                ->orderBy('name');
            }])
            ->orderBy('id')
            ->get()
            ->sortBy('id')
            ->values();

        $services = $categories->flatMap->services->values();

        $preferredCategoryId = 1;
        $defaultCategoryId = (int) ($categories->contains('id', $preferredCategoryId)
            ? $preferredCategoryId
            : optional($categories->first())->id);

        $store = Store::query()->select(['id', 'name', 'address', 'district', 'city'])->find($storeId);

        $storeAddress = collect([
            optional($store)->address,
            optional($store)->district,
            optional($store)->city,
        ])->filter()->implode(', ');

        return view('service.index', [
            'categories' => $categories,
            'defaultCategoryId' => $defaultCategoryId,
            'storeId' => $storeId,
            'store' => [
                'name' => optional($store)->name ?? 'Taman Palem',
                'address' => $storeAddress ?: 'Jl. Sunset Avenue No. 10, Cengkareng, Jakarta Barat',
            ],
            'servicePayload' => $this->servicePayload($services),
        ]);
    }

    /**
     * Prepare the service payload for the lightweight booking widget.
     */
    protected function servicePayload(Collection $services): Collection
    {
        return $services->map(fn (Service $service) => $service->only([
            'id',
            'name',
            'description',
            'duration',
            'price',
        ]));
    }
}
