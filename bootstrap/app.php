<?php
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php', // Assurez-vous que votre fichier api.php est bien référencé
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Configuration des alias de middleware pour les routes
        $middleware->alias([
            // Vos autres alias de middleware existants, si vous en avez (ex: 'role' => \App\Http\Middleware\CheckRole::class)
            'auth.api' => \Laravel\Sanctum\Http\Middleware\AuthenticateWithApiToken::class, // Par exemple, pour les routes API explicites
        ]);

        // Configuration du groupe de middleware 'api'
        $middleware->api(prepend: [
            // \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class, // Si c'est une SPA
        ]);

        // Vous pourriez avoir d'autres middlewares globaux ou de groupes ici
        // $middleware->group('api', [
        //     'throttle:api',
        //     \Illuminate\Routing\Middleware\SubstituteBindings::class,
        // ]);
        // Par défaut, le groupe 'api' dans withRouting applique déjà les middlewares nécessaires
        // (ThrottleRequests, SubstituteBindings). Sanctum est souvent géré via le alias
        // 'auth:sanctum' directement sur les routes.
        // La ligne suivante est la plus pertinente pour les API qui utilisent auth:sanctum
        $middleware->web(append: [
            // Vos middlewares web
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
