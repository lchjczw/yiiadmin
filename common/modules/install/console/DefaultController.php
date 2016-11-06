<?php
/**
 * Created by PhpStorm.
 * User: yidashi
 * Date: 16/7/6
 * Time: 下午3:35
 */

namespace install\console;


use yii\console\Controller;
use Yii;
use yii\helpers\Console;

/**
 * @property \install\Module $module
 */
class DefaultController extends Controller
{

    public $writablePaths = [
        '@root/runtime',
        '@root/web/assets',
        '@root/web/admin/assets',
        '@root/web/storage'
    ];

    public $executablePaths = [
        '@root/yii',
    ];

    public function actionSetWritable()
    {
        $this->setWritable($this->writablePaths);
    }

    public function actionSetExecutable()
    {
        $this->setExecutable($this->executablePaths);
    }

    public function actionSetKeys()
    {
        $this->module->setKeys($this->module->envPath);
    }

    public function setWritable($paths)
    {
        foreach ($paths as $writable) {
            $writable = Yii::getAlias($writable);
            Console::output("Setting writable: {$writable}");
            @chmod($writable, 0777);
        }
    }

    public function setExecutable($paths)
    {
        foreach ($paths as $executable) {
            $executable = Yii::getAlias($executable);
            Console::output("Setting executable: {$executable}");
            @chmod($executable, 0755);
        }
    }

    public function actionSetDb()
    {
        do {
            $dbHost = $this->prompt('数据库地址(默认为中括号内的值)' . PHP_EOL, ['default' => '127.0.0.1']);
            $dbPort = $this->prompt('端口(默认为中括号内的值)' . PHP_EOL, ['default' => '3306']);
            $dbDbname = $this->prompt('数据库名称(不存在则自动创建)' . PHP_EOL, ['default' => 'yii']);
            $dbUsername = $this->prompt('数据库用户名(默认为中括号内的值)' . PHP_EOL, ['default' => 'root']);
            $dbPassword = $this->prompt('数据库密码' . PHP_EOL);
            $dbDsn = "mysql:host={$dbHost};port={$dbPort}";
        } while(!$this->testConnect($dbDsn, $dbDbname, $dbUsername, $dbPassword));
        $dbDsn = "mysql:host={$dbHost};port={$dbPort};dbname={$dbDbname}";
        $dbTablePrefix = $this->prompt('数据库表前缀(默认为中括号内的值)' . PHP_EOL, ['default' => 'yii2cmf_']);
        $this->module->setEnv('DB_USERNAME', $dbUsername);
        $this->module->setEnv('DB_PASSWORD', $dbPassword);
        $this->module->setEnv('DB_TABLE_PREFIX', $dbTablePrefix);
        $this->module->setEnv('DB_DSN', $dbDsn);
        Yii::$app->set('db', Yii::createObject([
                'class' => 'yii\db\Connection',
                'dsn' => $dbDsn,
                'username' => $dbUsername,
                'password' => $dbPassword,
                'tablePrefix' => $dbTablePrefix,
                'charset' => 'utf8'
            ])
        );
    }
    public function testConnect($dsn = '', $dbname, $username = '', $password = '')
    {
        try{
            $pdo = new \PDO($dsn, $username, $password);
            $sql = "CREATE DATABASE IF NOT EXISTS {$dbname} DEFAULT CHARSET utf8 COLLATE utf8_general_ci;";
            $pdo->query($sql);
        } catch(\Exception $e) {
            $this->stderr("\n" . $e->getMessage(), Console::FG_RED);
            $this->stderr("\n  ... 连接失败,核对数据库信息.\n\n", Console::FG_RED, Console::BOLD);
            return false;
        }
        return true;
    }

    public function actionIndex()
    {
        if ($this->module->checkInstalled()) {
            $this->stdout("\n  ... 已经安装过.\n\n", Console::FG_RED);
            die;
        }
        $start = <<<STR
+==========================================+
| Welcome to setup yii2cmf         |
| 欢迎使用 yii2cmf 安装程序     |
+------------------------------------------+
| Follow the on-screen instructions please |
| 请按照屏幕上的提示操作以完成安装         |
+==========================================+

STR;
        $this->stdout($start, Console::FG_GREEN);
        copy(Yii::getAlias('@root/.env.example'), Yii::getAlias($this->module->envPath));
        $this->runAction('set-writable', ['interactive' => $this->interactive]);
        $this->runAction('set-executable', ['interactive' => $this->interactive]);
        $this->runAction('set-keys', ['interactive' => $this->interactive]);
        $this->runAction('set-db', ['interactive' => $this->interactive]);
        $appStatus = $this->select('设置当前应用模式', ['dev' => 'dev', 'prod' => 'prod']);
        $this->module->setEnv('YII_DEBUG', $appStatus == 'prod' ? 'false' : 'true');
        $this->module->setEnv('YII_ENV', $appStatus);
        Yii::$app->runAction('migrate/up', ['interactive' => false]);
        Yii::$app->runAction('cache/flush-all', ['interactive' => false]);
        $this->module->setInstalled();
        $end = <<<STR
+=================================================+
| Installation completed successfully, Thanks you |
| 安装成功，感谢选择和使用 yii2cmf              |
+-------------------------------------------------+
| 说明和注意事项：                                |
| 一些基本的设置可以在.env文件里修改
+=================================================+

STR;

        $this->stdout($end, Console::FG_GREEN);
    }

    public function actionReset()
    {
        @unlink(Yii::getAlias($this->module->installFile));
    }

    public function actionUpdate()
    {
        \Yii::$app->runAction('migrate/up', ['interactive' => $this->interactive]);
    }


}


