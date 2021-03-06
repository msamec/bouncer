<?php

namespace Silber\Bouncer\Middleware;

use Closure;
use Illuminate\Auth\Access\Gate;

class Authorize
{
    /**
     * The access gate instance.
     *
     * @var \Illuminate\Auth\Access\Gate
     */
    protected $gate;

    /**
     * Constructor.
     *
     * @param \Illuminate\Auth\Access\Gate  $gate
     */
    public function __construct(Gate $gate)
    {
        $this->gate = $gate;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $ability
     * @param  string|null  $model
     * @return mixed
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function handle($request, Closure $next, $ability = null, $model = null)
    {
        if ( ! $request->user()) {
            return $this->unauthorized($request);
        }

        $this->gate->authorize($ability, $this->getGateArguments($model));

        return $next($request);
    }

    /**
     * Get the arguments parameter for the gate.
     *
     * @param  string|null  $model
     * @return array|string|\Illuminate\Database\Eloquent\Model
     */
    protected function getGateArguments($model)
    {
        // If there's no model, we'll pass an empty array to the gate. If it
        // looks like a FQCN of a model, we'll send it to the gate as is.
        // Otherwise, we'll resolve the Eloquent model from the route.
        if (is_null($model)) {
            return [];
        }

        if (strpos($str, '\\') !== false) {
            return $model;
        }

        return $request->route($model);
    }

    /**
     * Create an unauthorized response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function unauthorized($request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return response('Unauthorized.', 401);
        } else {
            return redirect()->guest('login');
        }
    }
}
