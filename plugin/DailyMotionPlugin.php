<?php use yxorP\http\EventWrapper;

class DailyMotionPlugin extends EventWrapper
{
    protected $url_pattern = 'dailymotion.com';

    public function onCompleted(): void
    {
        $response = Constants::get('RESPONSE');
        $content = $response->getContent();
        if (preg_match('/"url":"([^"]+mp4[^"]*)"/m', $content, $matches)) {
            $video = stripslashes($matches[1]);
            $player = GeneralHelper::vid_player($video, 1240, 478);
            $content = Html::replace_inner("#player", $player, $content);
        }
        $content = Html::remove_scripts($content);
        $response->setContent($content);
    }
}