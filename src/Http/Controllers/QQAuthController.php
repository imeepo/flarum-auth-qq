<?php

namespace HamZone\QQAuth\Http\Controllers;

use Exception;
use Flarum\Forum\Auth\Registration;
use Flarum\Http\UrlGenerator;
use Flarum\Settings\SettingsRepositoryInterface;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\RedirectResponse;

use HamZone\QQAuth\Http\Controllers\QQResponseFactory;
use HamZone\QQAuth\Api\Controllers\QQController;
use HamZone\QQAuth\Api\Controllers\QQResourceController;

class QQAuthController implements RequestHandlerInterface
{
    /**
     * @var QQResponseFactory
     */
    protected $response;
    /**
     * @var SettingsRepositoryInterface
     */
    protected $settings;
    /**
     * @var UrlGenerator
     */
    protected $url;
    /**
     * @param QQResponseFactory $response
     * @param SettingsRepositoryInterface $settings
     * @param UrlGenerator $url
     */
    public function __construct(QQResponseFactory $response, SettingsRepositoryInterface $settings, UrlGenerator $url)
    {
        $this->response = $response;
        $this->settings = $settings;
        $this->url = $url;
    }
    /**
     * @param Request $request
     * @return ResponseInterface
     * @throws Exception
     */
    public function handle(Request $request): ResponseInterface
    {
        $redirectUri = $this->url->to('forum')->route('auth.qq');
        app('log')->debug( $redirectUri );
        $provider = new QQController([
            'clientId' => $this->settings->get('flarum-ext-auth-qq.app_id'),
            'secret' => $this->settings->get('flarum-ext-auth-qq.app_secret'),
            'redirect_uri' => $redirectUri,
        ]);

        $session = $request->getAttribute('session');
        $queryParams = $request->getQueryParams();
        $code = array_get($queryParams, 'code');

        $isMobile = false;

        if( strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ){
            $isMobile = true;
        }
        if (isset($_SERVER['HTTP_X_WAP_PROFILE'])) {
            $isMobile = true;
        }
        if (isset($_SERVER['HTTP_VIA'])) {
            $isMobile = stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
        }
        if (isset($_SERVER['HTTP_USER_AGENT'])){
            if(
                strpos($_SERVER['HTTP_USER_AGENT'], 'Mobile') !== false||
                strpos($_SERVER['HTTP_USER_AGENT'], 'Android') !== false||
                strpos($_SERVER['HTTP_USER_AGENT'], 'Kindle') !== false||
                strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mini') !== false||
                strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mobi') !== false||
                strpos($_SERVER['HTTP_USER_AGENT'], 'BlackBerry') !== false
            ){
                $isMobile = true;
            }
        }

        if (!$code) {
            $authUrl = $provider->getAuthorizationUrl();
            $session->put('oauth2state', $provider->getState());
            return new RedirectResponse($authUrl);
        }

        $state = array_get($queryParams, 'state');

        if (!$state || $state !== $session->get('oauth2state')) {
            $session->remove('oauth2state');
            throw new Exception('Invalid state');
        }

        $token = $provider->getAccessToken('authorization_code', compact('code'));

        $fetchOpenId = $provider->fetchOpenId($token);
        $openId = $fetchOpenId['openid'];

        // /** @var QQResourceController $user */
        $user = $provider->getResourceOwnerDetailsUrl($token, $openId);

        app('log')->debug( $user['nickname']. $user['figureurl_qq_1']);
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        
        app('log')->debug( $_SERVER['HTTP_USER_AGENT'] );
        app('log')->debug( $isMobile );

        return $this->response->make(
            'QQ',
            $openId ,
            function (Registration $registration) use ($user) {
                $registration
                    ->suggestUsername($user['nickname'])
                    ->setPayload($user);

                $registration->provideAvatar($user['figureurl_qq_1']);
            },
            $isMobile,
            $protocol.$_SERVER['HTTP_HOST']
        );
    }
}