<?php

namespace App\Http\Controllers\Client\Demand;
use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Models\Order\Demand;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class CartController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index(): \Illuminate\Foundation\Application|Response|Application|ResponseFactory
    {
        return response(getCart());
    }

    public function removeItem(Demand $demand): \Illuminate\Foundation\Application|Response|Application|ResponseFactory
    {
        if ($demand->status != Status::in_cart->name){
            abort(ResponseAlias::HTTP_BAD_REQUEST, "Impossible de supprimer cette demande du panier");
        }

        return response(removeDemandFromDemand($demand));
    }

    public function emptyCart(): \Illuminate\Foundation\Application|Response|Application|ResponseFactory
    {
        return response(emptyCart(true));
    }
}
