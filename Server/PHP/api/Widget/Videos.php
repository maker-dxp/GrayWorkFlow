<?php

class Widget_Videos extends Zen_Widget {
    /**
     * 视频文件信息
     *
     * @var array
     */
    private $_data = array();

    /**
     * video_id
     *
     * @var int
     */
    private $_vid = 0;

    /**
     * @var Zen_DB
     */
    private $_db;

    /**
     * Widget_Videos constructor.
     * 获取视频文件信息
     *
     * @param int $video_id
     * @throws Zen_DB_Exception
     */
    public function __construct(int $video_id = 0) {
        $this->_db = Zen_DB::get(AUTH_MAIN);

        if($video_id != 0) {
            $this->_vid = $video_id;
            $sql = $this->_db
                ->select()
                ->from('videos')
                ->where('video_id = ?', $this->_vid);
            $this->_data = ($ret = $this->_db->query($sql)) ? $ret : array();
        }else {
            $this->_vid = 0;
            $this->_data = array();
        }
    }
}