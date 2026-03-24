<?php

namespace App\View\Components;

use App\Models\Listing;
use Illuminate\View\Component;

class SimilarProperties extends Component
{
    public $similarProperties;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($city, $type)
    {
        $this->similarProperties = Listing::select(
            'listing_title',
            'product_code',
            'images',
            'property_price',
            'heading_details',
            'bedroom',
            'bathroom',
            'garage',
            'address',
            'city',
            'state',
            'slug',
            'listingtype',
        )
            ->where('city', $city)
            ->where('available', 1)
            ->where('status', 1)
            ->where('listingtype', $type)
            ->inRandomOrder()
            ->limit(3)
            ->get();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.similar-properties');
    }
}
