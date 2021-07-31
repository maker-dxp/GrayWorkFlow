<?php

/* 任务状态 */
const S_NEED        =       0;
const S_WORKING     =       1;
const S_PROOF       =       2;
const S_DONE        =       3;

/* 工作类型 */
const J_TRANS       =       0;
const J_AXIS        =       1;
const J_COVER       =       2;
const J_AFTEREFFECT =       3;
const J_COMPRESS    =       4;

/* 项目状态 */
const PROJ_CREATE   =       0;
const PROJ_WORKING  =       1;
const PROJ_PROOF    =       2;
const PROJ_UPLOAD   =       3;
const PROJ_DONE     =       4;
const PROJ_DELETE   =       5;

/* 任务优先级 */
const L_NORMAL      =       0;
const L_PRIORITY    =       10;
const L_URGENT      =       100;

class Widget_Project extends Zen_Widget {

}