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
        unset($params['src']);

        $style = null;
        $transformation = null;
        $var = null;
        $default = null;

        if (isset($params['default'])) {
            $default = $params['default'];
            $src = $default;
            unset($params['default']);
        }
        if (isset($params['var'])) {
            $var = $params['var'];
            unset($params['var']);
        }
        if (isset($params['transformation'])) {
            $transformation = $params['transformation'];
            unset($params['transformation']);
        }
        if (isset($params['style']) && $params['style']) {
            $style = $params['style'];
            unset($params['style']);
        }

        $app = $smarty->getTemplateVars('app');
        if (!isset($app['system'])) {
            throw new Exception('Could not load asset #' . $asset->getId() . ': system is not available in the app variable');
        }

        $dependencyInjector = $app['system']->getDependencyInjector();
        $imageUrlGenerator = $dependencyInjector->get('ride\\library\\image\\ImageUrlGenerator');
        $image = null;

        // check for overriden style image
        if ($style) {
            $image = $asset->getStyleImage($style);
            if ($image) {
                // get url for the provided image
                $src = $imageUrlGenerator->generateUrl($image, $transformation, $params);
            }
        }

        if (!$image) {
            // no style image
            $image = $asset->getImage();
            if (!$image && $default) {
                $image = $default;
            }

            if (!$image) {
                // no image resolved
                $src = null;
            } else {
                // gather style transformations
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

                // add template transformation
                if ($transformation) {
                    if (isset($transformations[$transformation])) {
                        unset($transformations[$transformation]);
                    }

                    $transformations[$transformation] = $params;
                }

                // get url for the provided image
                $src = $imageUrlGenerator->generateUrl($image, $transformations);
            }
        }

        if ($var === null) {
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
