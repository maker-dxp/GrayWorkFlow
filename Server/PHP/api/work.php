<?php

/**
 * 该文件用于完成视频字幕工作区模块
 */

include_once 'db.php';

const FIELD_VIDEO = array(
    'vid' => 'vid',
    'video_name' => 'VideoName',
    'video_describe' => 'VideoDescribe',
    'video_origin_file' => 'OriginFile',
    'video_final_file' => 'FinalFile',
    'trans_title' => 'TranslatedTitle',
    'trans_draft' => 'TranslateDraftFile',
    'trans_proof' => 'TranslateProofFile',
    'trans_final' => 'TranslateFinalFile',
    'axis_draft' => 'AxisDraftFile',
    'axis_proof' => 'AxisProofFile',
    'axis_final' => 'AxisFinalFile',
    'cover_untranslated' => 'UntranslatedCover',
    'cover_translated' => 'TranslatedCover'
);

const FIELD_TASK = array(
    'tid' => 'tid',
    'video' => 'vid',
    'video_upload_user' => 'Uploader',
    'translate_status' => 'TranslateStatus',
    'trans_maker_user' => 'Translator',
    'trans_proof_user' => 'TransProofreader',
    'axis_status' => 'AxisStatus',
    'axis_maker_user' => 'AxisMaker',
    'axis_proof_user' => 'AxisProofreader',
    'cover_status' => 'CoverStatus',
    'cover_trans_user' => 'CoverTranslator',
    'cover_proof_user' => 'CoverProofreader',
    'video_status' => 'VideoStatus',
    'effect_maker_user' => 'EffectiveMaker',
    'video_maker_user' => 'VideoMaker'
);

const CREATE = 'created';
const WANTED = 'wanted';
const DRAFT  = 'draft';
const PROOF  = 'proof';
const FINISH = 'complete';
const NOBODY = 0;
const SU     = 1;

//每页数目
const NUM_PER_PAGE  =   10;

/**
 * 增加一个新的视频
 *
 * 需要传入的参数有：
 *  VideoName
 *  VideoDescribe
 *  OriginFile
 */
function addVideo(array $videoInfo): bool {
    switch(false) {
        case isset($videoInfo['VideoName']):
        case isset($videoInfo['VideoDescribe']):
        case isset($videoInfo['OriginFile']):
            sendResponse(LOSE_SOME_INFO);
    }

    $ret = DB::get()
        ->prepare("
            INSERT INTO `videos`(
                    `vid`,
                    `video_name`,
                    `video_describe`,
                    `video_origin_file`
            )
            SELECT 
                 IFNULL(MAX(`vid`), 0) + 1,
                 :video_name,
                 :describe,
                 :origin_file
                 FROM `videos`;
        ")->execute(array(
            'video_name' => $videoInfo['VideoName'],
            'describe' => $videoInfo['VideoDescribe'],
            'origin_file' => $videoInfo['OriginFile']
        ));

    if(!$ret) { return false; }

    return true;
}

/**
 * 更新视频信息
 *
 * 需要传入的参数有：
 *  vid
 * 其他参数可选
 */
function updateVideo(array $videoInfo): bool {
    //初始化查询id
    if(!isset($videoInfo['vid'])) {
        sendResponse(LOSE_SOME_INFO);
    }
    $vid = $videoInfo['vid'];

    if(!videoExist($vid)){
        sendResponse(VIDEO_NOT_FOUND);
    }
    $infos = getVideoInfo($vid);
    foreach ($infos as $key => $value) {
        if(!isset($videoInfo[FIELD_VIDEO[$key]])) {
            continue;
        }
        //更新数据
        $infos[$key] = $videoInfo[FIELD_VIDEO[$key]];
    }

    $ret = DB::get()
        ->prepare("
            UPDATE `videos`
            SET 
              `video_name` = :video_name,
              `video_describe` = :video_describe,
              `video_origin_file` = :video_origin_file,
              `video_final_file` = :video_final_file,
              `trans_title` = :trans_title,
              `trans_draft` = :trans_draft,
              `trans_proof` = :trans_proof,
              `trans_final` = :trans_final,
              `axis_draft` = :axis_draft,
              `axis_proof` = :axis_proof,
              `axis_final` = :axis_final,
              `cover_untranslated` = :cover_untranslated,
              `cover_translated` = :cover_translated
            WHERE `vid` = :vid;")
        ->execute(array(
            'video_name' => $infos['video_name'],
            'video_describe' => $infos['video_describe'],
            'video_origin_file' => $infos['video_origin_file'],
            'video_final_file' => $infos['video_final_file'],
            'trans_title' => $infos['trans_title'],
            'trans_draft' => $infos['trans_draft'],
            'trans_proof' => $infos['trans_proof'],
            'trans_final' => $infos['trans_final'],
            'axis_draft' => $infos['axis_draft'],
            'axis_proof' => $infos['axis_proof'],
            'axis_final' => $infos['axis_final'],
            'cover_untranslated' => $infos['cover_untranslated'],
            'cover_translated' => $infos['cover_translated'],
            'vid' => $vid
        ));
    if(!$ret) {
        return false;
    }

    return true;
}

/**
 * 获取视频信息
 */
function getVideoInfo(int $vid): array{
    return (array)DB::get()
        ->prepare("
                SELECT * 
                FROM `videos` 
                WHERE `vid` = :vid;")
        ->execute(array(
            'vid' => $vid
        ))->fetch(PDO::FETCH_ASSOC);
}

/**
 * 获取所有视频信息
 */
function getVideoList(int $page): array{
    $offset = $page * NUM_PER_PAGE;
    return DB::get()
        ->prepare("
                SELECT `vid`, `video_name`, `video_describe`, `trans_title` 
                FROM `videos` 
                LIMIT 10 OFFSET :offset;")
        ->execute(['offset' => $offset])
        ->fetchAll();
}

function videoExist(int $vid): bool {
    $ret = DB::get()
        ->prepare("SELECT `vid` FROM `videos` WHERE `vid` = :vid;")
        ->execute(array(
            'vid' => $vid
        ))->fetch();
    if($ret) {
        return true;
    }else{
        return false;
    }
}

/**
 * 创建一个新的任务
 *
 * 需要传递的参数：
 *  vid
 */
function createJob(int $vid): bool {
    if(jobExist($vid)) {
        sendResponse(JOB_EXIST);
    }

    return (bool)DB::get()
        ->prepare(
            'INSERT INTO `tasks`
                 SELECT  IFNULL(MAX(`tid`), 0),
                         :vid,
                         :video_upload_user,
                         :translate_status,
                         :trans_maker_user,
                         :trans_proof_user,
                         :axis_status,
                         :axis_maker_user,
                         :axis_proof_user,
                         :cover_status,
                         :cover_trans_user,
                         :cover_proof_user,
                         :video_status,
                         :effect_maker_user,
                         :video_maker_user
                         FROM `tasks`;')
        ->execute(array(
            'vid' => $vid,
            'video_upload_user' => NOBODY,
            'translate_status' => CREATE,
            'trans_user' => NOBODY,
            'trans_proof_user' => NOBODY,
            'axis_status' => CREATE,
            'axis_user' => NOBODY,
            'axis_proof_user' => NOBODY,
            'cover_status' => CREATE,
            'cover_user' => NOBODY,
            'cover_proof_user' => NOBODY,
            'video_status' => CREATE,
            'effect_maker_user' => NOBODY,
            'video_maker_user' => NOBODY
        ));
}

function updateJob(array $jobInfo) {
    if(!isset($jobInfo['jid'])) {
        sendResponse(LOSE_SOME_INFO);
    }
    $jid = $jobInfo['jid'];

    $infos = getJobInfo($jid);
    foreach ($infos as $key => $value) {
        if(!isset($jobInfo[FIELD_JOB[$key]])) {
            continue;
        }
        //更新数据
        $infos[$key] = $jobInfo[FIELD_JOB[$key]];
    }

    return DB::get()
        ->prepare("
            UPDATE `tasks`
            SET
                `video_upload_user` = :video_upload_user,
                `translate_status` = :translate_status,
                `trans_user` = :trans_user,
                `trans_proof_user` = :trans_proof_user,
                `axis_status` = :axis_status,
                `axis_user` = :axis_user,
                `axis_proof_user` = :axis_proof_user,
                `cover_status` = :cover_status,
                `cover_user` = :cover_user,
                `cover_proof_user` = :cover_proof_user
            WHERE `tid` = :jid;
        ")->execute(array(
            'video_upload_user' => $infos['video_upload_user'],
            'translate_status' => $infos['translate_status'],
            'trans_user' => $infos['trans_user'],
            'trans_proof_user' => $infos['trans_proof_user'],
            'axis_status' => $infos['axis_status'],
            'axis_user' => $infos['axis_user'],
            'axis_proof_user' => $infos['axis_proof_user'],
            'cover_status' => $infos['cover_status'],
            'cover_user' => $infos['cover_user'],
            'cover_proof_user' => $infos['cover_proof_user']
        ));
}

function jobExist(int $vid): bool {
    $ret = DB::get()
        ->prepare('SELECT `video` FROM `tasks` WHERE `video` = :vid;')
        ->execute(array(
            'vid' => $vid
        ))->fetch();

    if($ret){
        return true;
    }else{
        return false;
    }
}

/**
 * 获取任务信息
 */
function getJobInfo(int $jid): array {
    return (array)DB::get()
        ->prepare("
            SELECT * 
            FROM `tasks` 
            WHERE `tid` = :jid;")
        ->execute(array(
            'jid' => $jid
        ))->fetch(PDO::FETCH_ASSOC);
}

/**
 * 按页获取任务信息
 */
function getJobList(int $page): array {
    $offset = $page * NUM_PER_PAGE;
    return DB::get()
        ->prepare("
            SELECT `tasks`.`tid`, `tasks`.`translate_status`, `tasks`.`axis_status`, `tasks`.`cover_status`,
                   `videos`.`video_name`, `videos`.`trans_title`, `videos`.`video_describe`
            FROM `tasks` 
            INNER JOIN `videos`
            ON `tasks`.`video` = `videos`.`vid`
            LIMIT 10 OFFSET :offset;")
        ->execute(['offset' => $offset])
        ->fetchAll();
}

function createVideo() {
    if(!(isLogin())) {
        sendHttpStatus(403);
        sendResponse(NOT_LOGGED_IN);
    }

    if(!($raw = getRawData())) {
        sendHttpStatus(400);
        sendResponse(EMPTY_BODY);
    }

    if(!($req = parseRawJson($raw))) {
        sendHttpStatus(400);
        sendResponse(WRONG_JSON);
    }

    $videoInfo = $req['VideoInfo'];
    $data = addVideo($videoInfo);
    sendResponse($data ? OK : LOSE_SOME_INFO);
}

function getVideo() {
    if(!(isLogin())) {
        sendHttpStatus(403);
        sendResponse(NOT_LOGGED_IN);
    }

    if(!($raw = getRawData())) {
        sendHttpStatus(400);
        sendResponse(EMPTY_BODY);
    }

    if(!($req = parseRawJson($raw))) {
        sendHttpStatus(400);
        sendResponse(WRONG_JSON);
    }

    $vid = $req['vid'];
    $data = getVideoInfo($vid);
    sendResponse(OK, $data);
}

function changeVideo() {
    if(!(isLogin())) {
        sendHttpStatus(403);
        sendResponse(NOT_LOGGED_IN);
    }

    if(!($raw = getRawData())) {
        sendHttpStatus(400);
        sendResponse(EMPTY_BODY);
    }

    if(!($req = parseRawJson($raw))) {
        sendHttpStatus(400);
        sendResponse(WRONG_JSON);
    }

    $videoInfo = $req['VideoInfo'];
    $data = updateVideo($videoInfo);
    sendResponse($data ? OK : LOSE_SOME_INFO);
}