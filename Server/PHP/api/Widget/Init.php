<?php


class Widget_Init extends Zen_Widget{
    /**
     * 全局初始化
     */
    public function init() {
        include_once __DIR__ . DIRECTORY_SEPARATOR . 'widget.config.php';   //载入组件配置
        Zen_DB::init();
        Zen_Loader::addClassMap(CLASS_MAP);
        $options = new Widget_Options();

        /*
         * 处理路由表
         */
        switch(true) {
            case WIDGET_DEBUG:
            case (!$options->keyEmpty('RouteTable')):
                Zen_Router::setRouteTable();
                $options->setOption('RouteTable', Zen_Router::getRouteTable());
                break;
            default:
                $data = $options->getOption('RouteTable');
                Zen_Router::init($data);
        }
    }
}