<?php
/**
 * Created by PhpStorm.
 * User: yidashi
 * Date: 2016/11/6
 * Time: 下午6:17
 */

namespace install;

use Yii;
use yii\base\BootstrapInterface;

class Module extends \yii\base\Module implements BootstrapInterface
{
    public function bootstrap($app)
    {
        if ($app instanceof \yii\web\Application) {
            $app->getUrlManager()->addRules([
                ['class' => 'yii\web\UrlRule', 'pattern' => $this->id, 'route' => $this->id . '/default/index'],
                ['class' => 'yii\web\UrlRule', 'pattern' => $this->id . '/<controller:[\w\-]+>/<action:[\w\-]+>', 'route' => $this->id . '/<controller>/<action>'],
            ], false);
        } elseif ($app instanceof \yii\console\Application) {
            $this->controllerNamespace = 'install\console';
        }
    }

    public $layout = '@install/views/layouts/main';

    public $installFile = '@root/web/install.txt';

    public $envPath = '@root/.env';

    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            if ($this->checkInstalled() == true && Yii::$app instanceof \yii\web\Application) {
                Yii::$app->getResponse()->redirect(['/']);
                return false;
            }
            return true;
        }
    }

    public function checkInstalled()
    {
        return file_exists(Yii::getAlias($this->installFile));
    }

    public function setInstalled()
    {
        file_put_contents(Yii::getAlias($this->installFile), time());
    }

    public function setKeys($file)
    {
        $file = Yii::getAlias($file);
        $content = file_get_contents($file);
        $content = preg_replace_callback('/<generated_key>/', function () {
            $length = 32;
            $bytes = openssl_random_pseudo_bytes(32, $cryptoStrong);
            return strtr(substr(base64_encode($bytes), 0, $length), '+/', '_-');
        }, $content);
        file_put_contents($file, $content);
    }

    public function setEnv($name, $value)
    {
        $file = Yii::getAlias($this->envPath);
        $content = preg_replace("/({$name}\s*=)\s*(.*)/", "\\1$value", file_get_contents($file));
        file_put_contents($file, $content);
    }
}