<?php

use ride\application\orm\asset\entry\AssetEntry;

function smarty_function_asset($params, &$smarty) {
    static $service = null;

    try {
        if (!isset($params['src']) || !$params['src']) {
            throw new Exception('Could not add asset: no src parameter provided');
        } elseif (!$params['src'] instanceof AssetEntry) {
            throw new Exception('Could not add asset: no AssetEntry provided');
        } else {
            $asset = $params['src'];
            unset($params['src']);
        }

        $style = null;
        if (isset($params['style']) && $params['style']) {
            $style = $params['style'];
            unset($params['style']);
        }

        $var = null;
        if (isset($params['var'])) {
            $var = $params['var'];
            unset($params['var']);
        }

        if (!$service) {
            $app = $smarty->getTemplateVars('app');
            if (!isset($app['system'])) {
                throw new Exception('Could not load asset #' . $asset->getId() . ': system is not available in the app variable');
            }

            $dependencyInjector = $app['system']->getDependencyInjector();
            $service = $dependencyInjector->get('ride\\service\\AssetService');
        }

        try {
            $url = $service->getAssetUrl($asset, $style, true);
        } catch (Exception $exception) {
            $log = $app['system']->getDependencyInjector()->get('ride\\library\\log\\Log');
            $log->logException($exception);

            $url = null;
        }

        if ($var === null) {
            return $url;
        } else {
            $smarty->assign($var, $url);
        }
    } catch (Exception $exception) {
        $app = $smarty->getTemplateVars('app');
        if (isset($app['system'])) {
            $log = $app['system']->getDependencyInjector()->get('ride\\library\\log\\Log');
            $log->logException($exception);
        }
    }
}
