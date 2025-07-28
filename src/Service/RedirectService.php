<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RedirectService {
  public static $redirectParam = "redirectTo";

  public function __construct(private UrlGeneratorInterface $urlGenerator) {
  }

  public function safeRedirect(
    Request $request,
    ?string $defaultRoute = null,
    array $defaultRouteParams = []
  ): RedirectResponse {
    $redirectUrl = $request->query->get(RedirectService::$redirectParam);

    if ($redirectUrl && $this->isSafeUrl($request, $redirectUrl)) {
      return new RedirectResponse($redirectUrl);
    }

    if ($defaultRoute) {
      return new RedirectResponse(
        $this->urlGenerator->generate($defaultRoute, $defaultRouteParams)
      );
    }

    return new RedirectResponse($request->getUri());
  }

  private function isSafeUrl(Request $request, string $url): bool {
    if (str_starts_with($url, "/")) {
      return true;
    }

    $urlHost = parse_url($url, PHP_URL_HOST);
    $requestHost = $request->getHost();

    return $urlHost === $requestHost;
  }
}
