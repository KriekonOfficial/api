<?php

namespace Core\Router;

use \GuzzleHttp\Psr7\ServerRequest;

class RouterLib
{
    /**
    * Allows for some to specify the first part of a url before we attempt to set an allowed version.
    * If Version is empty we will fall back to see if it is allowed version
    * If not an allowed version we fall back again to the Root Folder.
    */
    public static function parseURI(RouterURI $uri) : RouterURI
    {
    	$server = Router::getRequest()::getServer();
        $url = self::formNewURL($server);

        if ($uri->getVersion() == '' && in_array(($url[0] ?? ''), $uri->getAllowedVersions()))
        {
            $uri->setVersion(ucwords($url[0]));
        }

        if ($uri->getVersion() != '')
        {
            $uri->setPropsVersionURL($url);
        }
        else
        {
            $uri->setPropsRootURL($url);
        }

        return $uri;
    }

	private static function formNewURL(ServerRequest $server) : array
	{
		$uri = $server->getUri();

		$url = explode('/', strtolower(rtrim(ltrim($uri->getPath(), '/'), '/')));

		$new_url = [];

		if ($uri->getQuery() != '')
		{
			$new_url['additional_params'] = $server->getQueryParams();
		}

		$new_url += $url;

		return $new_url;
	}
}