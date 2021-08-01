<?php

class Widget_Init extends Zen_Widget{
    /**
     * 全局初始化
     */
    public function init() {
        /*
         * 处理异常和错误
         */
        set_exception_handler(array('Widget_Error_Handle', 'exceptionHandle'));
        set_error_handler(array('Widget_Error_Handle', 'errorHandle'), E_ERROR | E_WARNING);
        register_shutdown_function(array('Widget_Error_Handle', 'fatalErrorHandle'));

        /*
         * 处理组件配置
         */
        include_once __DIR__ . DIRECTORY_SEPARATOR . 'widget.config.php';   //载入组件配置
        Zen_DB::init();
        Zen_Loader::addClassMap(CLASS_MAP);
        $options = new Widget_Options();

        /*
         * 处理路由表
         */
        switch(true) {
            case WIDGET_DEBUG:
            case ($options->keyEmpty('RouteTable') || $options->keyEmpty('RouteIgnore')):
                Zen_Router::setRouteIgnore(PHP_IGNORE, true);
                Zen_Router::setRouteTable();
                $options->setOption('RouteTable', Zen_Router::getRouteTable());
                $options->setOption('RouteIgnore', Zen_Router::getRouteIgnore());
                break;
            default:
                $map = $options->getOption('RouteTable');
                $ignore = $options->getOption('RouteIgnore');
                Zen_Router::init($map, $ignore);
        }
    }
}