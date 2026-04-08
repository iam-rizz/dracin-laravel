<?php

namespace App\Http\Controllers;

use App\Services\Providers\DramaBoxService;
use App\Services\Providers\ReelShortService;
use App\Services\Providers\ShortMaxService;
use App\Services\Providers\NetShortService;
use App\Services\Providers\MeloloService;
use App\Services\Providers\FreeReelsService;
use App\Services\Providers\DramaNovaService;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __construct(
        protected DramaBoxService $dramaBox,
        protected ReelShortService $reelShort,
        protected ShortMaxService $shortMax,
        protected NetShortService $netShort,
        protected MeloloService $melolo,
        protected FreeReelsService $freeReels,
        protected DramaNovaService $dramaNova,
    ) {}

    public function index(Request $request)
    {
        $query = $request->get('q', '');
        $provider = $request->get('provider', 'all');

        if (empty($query)) {
            return view('search', ['query' => '', 'results' => [], 'provider' => $provider]);
        }

        $results = [];

        if ($provider === 'all' || $provider === 'dramabox') {
            $data = $this->dramaBox->search($query);
            $results['dramabox'] = $data;
        }
        if ($provider === 'all' || $provider === 'reelshort') {
            $data = $this->reelShort->search($query);
            $results['reelshort'] = $data;
        }
        if ($provider === 'all' || $provider === 'shortmax') {
            $data = $this->shortMax->search($query);
            $results['shortmax'] = $data;
        }
        if ($provider === 'all' || $provider === 'netshort') {
            $data = $this->netShort->search($query);
            $results['netshort'] = $data;
        }
        if ($provider === 'all' || $provider === 'melolo') {
            $data = $this->melolo->search($query);
            $results['melolo'] = $data;
        }
        if ($provider === 'all' || $provider === 'freereels') {
            $data = $this->freeReels->search($query);
            $results['freereels'] = $data;
        }
        if ($provider === 'all' || $provider === 'dramanova') {
            $data = $this->dramaNova->search($query);
            $results['dramanova'] = $data;
        }

        return view('search', compact('query', 'results', 'provider'));
    }
}
