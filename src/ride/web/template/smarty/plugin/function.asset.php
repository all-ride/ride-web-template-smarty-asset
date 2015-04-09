<?php

use \ride\application\orm\asset\entry\AssetEntry;

function smarty_function_asset($params, &$smarty) {
    static $styles = array();

    $src = null;

    try {
        if (!isset($params['src']) || !$params['src']) {
            throw new Exception('Could not add asset: no src parameter provided');
        } elseif (!$params['src'] instanceof AssetEntry) {
            throw new Exception('Could not add asset: no AssetEntry provided');
        }
        $asset = $params['src'];

        if (!isset($params['style']) || !$params['style']) {
            throw new Exception('Could not add asset: no style parameter provided');
        }
        $style = $params['style'];

        $var = null;
        if (!empty($params['var'])) {
            $var = $params['var'];
        }

        $app = $smarty->getTemplateVars('app');
        if (!isset($app['system'])) {
            throw new Exception('Could not load asset #' . $asset->getId() . ': system is not available in the app variable');
        }

        $dependencyInjector = $app['system']->getDependencyInjector();
        $imageUrlGenerator = $dependencyInjector->get('ride\\library\\image\\ImageUrlGenerator');

        $image = $asset->getStyleImage($style);
        if ($image) {
            $src = $imageUrlGenerator->generateUrl($image);
        } else {
            $image = $asset->getImage();
            if (!$image) {
                $src = null;
            } else {
                if (isset($styles[$style])) {
                    $transformations = $styles[$style];
                } else {
                    $ormManager = $dependencyInjector->get('ride\\library\\orm\\OrmManager');
                    $styleModel = $ormManager->getImageStyleModel();

                    $style = $styleModel->getBy(array('filter' => array('slug' => $style)));
                    if (!$style) {
                        throw new Exception('Could not load asset #' . $asset->getId() . ': style ' . $style . ' is not available');
                    }

                    $transformations = $style->getTransformationArray();

                    $styles[$style->getSlug()] = $transformations;
                }

                $src = $imageUrlGenerator->generateUrl($image, $transformations);
            }
        }

        if ($var == null) {
            return $src;
        } else {
            $smarty->assign($var, $src);
        }
    } catch (Exception $exception) {
        $app = $smarty->getTemplateVars('app');
        if (isset($app['system'])) {
            $log = $app['system']->getDependencyInjector()->get('ride\\library\\log\\Log');
            $log->logException($exception);
        }
    }
}
