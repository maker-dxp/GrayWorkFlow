<?php


class Widget_Api_Entry extends Widget_Api {
    /**
     * 登录状态
     *
     * @var bool
     */
    private $_is_login = false;

    /**
     * @map('/api/user/login')
     */
    public static function Login() {

    }
}