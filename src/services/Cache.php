<?php

namespace trydig\craftimageshop\services;

use Craft;
use yii\base\Component;
use yii\caching\FileCache;

class Cache extends Component
{

  private FileCache $_cache;

  function __construct()
  {
    $this->_cache = new FileCache();
    $this->_cache->keyPrefix = 'ImageShop_';
    $this->_cache->defaultDuration = 86400;
    $this->_cache->cachePath = Craft::$app->getPath()->getRuntimePath() . '/cache_imageshop';
  }

  public function getCache()
  {
    return $this->_cache;
  }
}
