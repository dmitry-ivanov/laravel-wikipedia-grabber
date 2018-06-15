<?php

namespace Illuminated\Wikipedia\WikipediaGrabber\Tests\Grabber\Component;

use Illuminated\Wikipedia\Grabber\Component\Image;
use Illuminated\Wikipedia\WikipediaGrabber\Tests\TestCase;

class ImageTest extends TestCase
{
    /** @test */
    public function it_has_position_which_defaults_to_right()
    {
        $image = new Image('url', 100, 200, 'original');
        $this->assertEquals('right', $image->getPosition());
    }

    /** @test */
    public function position_can_be_set_to_left_value()
    {
        $image = new Image('url', 100, 200, 'original', 'left');
        $this->assertEquals('left', $image->getPosition());
    }

    /** @test */
    public function but_if_passed_position_is_unknown_it_would_be_set_to_right()
    {
        $image = new Image('url', 100, 200, 'original', 'foobar');
        $this->assertEquals('right', $image->getPosition());
    }

    /** @test */
    public function passed_mime_would_be_automatically_lowercase()
    {
        $image = new Image('url', 100, 200, 'original', 'left', 'description', 'IMAGE/JPEG');
        $this->assertEquals('image/jpeg', $image->getMime());
    }

    /** @test */
    public function it_has_get_description_method_which_returns_beautified_filename_for_audio_files_if_description_was_empty()
    {
        $audio = new Image('thumb-url', 100, 200, 'http://example.com/some_audio_file.oga');
        $this->assertEquals('Some Audio File', $audio->getDescription());
    }

    /** @test */
    public function however_if_description_was_set_for_audio_file_then_it_would_be_used()
    {
        $audio = new Image('thumb-url', 100, 200, 'http://example.com/some_audio_file.oga', 'right', 'Description for audio');
        $this->assertEquals('Description for audio', $audio->getDescription());
    }

    /** @test */
    public function it_has_get_description_method_which_returns_beautified_filename_for_video_files_if_description_was_empty()
    {
        $video = new Image('thumb-url', 100, 200, 'http://example.com/some_video_file.ogv');
        $this->assertEquals('Some Video File', $video->getDescription());
    }

    /** @test */
    public function however_if_description_was_set_for_video_file_then_it_would_be_used()
    {
        $video = new Image('thumb-url', 100, 200, 'http://example.com/some_video_file.ogv', 'right', 'Description for video');
        $this->assertEquals('Description for video', $video->getDescription());
    }

    /** @test */
    public function it_has_get_alt_method_which_escapes_quotes_in_description()
    {
        $image = new Image('url', 100, 200, 'original', 'foobar', 'Description with single quote \' and double quote "!');

        $this->assertEquals('Description with single quote &#039; and double quote &quot;!', $image->getAlt());
    }

    /** @test */
    public function it_has_is_audio_method_which_returns_true_for_oga_and_mp3_and_wav_file_extensions()
    {
        $oga = new Image('http://example.com/thumb.oga.jpg', 100, 200, 'http://example.com/file.oga');
        $mp3 = new Image('http://example.com/thumb.mp3.jpg', 100, 200, 'http://example.com/file.mp3');
        $wav = new Image('http://example.com/thumb.wav.jpg', 100, 200, 'http://example.com/file.wav');

        $this->assertTrue($oga->isAudio());
        $this->assertTrue($mp3->isAudio());
        $this->assertTrue($wav->isAudio());
    }

    /** @test */
    public function and_is_audio_returns_false_for_jpg_and_other_image_extensions()
    {
        $jpg = new Image('http://example.com/file.thumb.jpg', 100, 200, 'http://example.com/file.jpg');
        $png = new Image('http://example.com/file.thumb.png', 100, 200, 'http://example.com/file.png');
        $gif = new Image('http://example.com/file.thumb.gif', 100, 200, 'http://example.com/file.gif');

        $this->assertFalse($jpg->isAudio());
        $this->assertFalse($png->isAudio());
        $this->assertFalse($gif->isAudio());
    }

    /** @test */
    public function it_has_is_video_method_which_returns_true_for_ogv_and_mp4_and_webm_file_extensions()
    {
        $ogv = new Image('http://example.com/thumb.ogv.jpg', 100, 200, 'http://example.com/file.ogv');
        $mp4 = new Image('http://example.com/thumb.mp4.jpg', 100, 200, 'http://example.com/file.mp4');
        $webm = new Image('http://example.com/thumb.webm.jpg', 100, 200, 'http://example.com/file.webm');

        $this->assertTrue($ogv->isVideo());
        $this->assertTrue($mp4->isVideo());
        $this->assertTrue($webm->isVideo());
    }

    /** @test */
    public function and_is_video_returns_false_for_jpg_and_other_image_extensions()
    {
        $jpg = new Image('http://example.com/file.thumb.jpg', 100, 200, 'http://example.com/file.jpg');
        $png = new Image('http://example.com/file.thumb.png', 100, 200, 'http://example.com/file.png');
        $gif = new Image('http://example.com/file.thumb.gif', 100, 200, 'http://example.com/file.gif');

        $this->assertFalse($jpg->isVideo());
        $this->assertFalse($png->isVideo());
        $this->assertFalse($gif->isVideo());
    }

    /** @test */
    public function is_audio_returns_false_for_video_files()
    {
        $ogv = new Image('http://example.com/thumb.ogv.jpg', 100, 200, 'http://example.com/file.ogv');
        $mp4 = new Image('http://example.com/thumb.mp4.jpg', 100, 200, 'http://example.com/file.mp4');
        $webm = new Image('http://example.com/thumb.webm.jpg', 100, 200, 'http://example.com/file.webm');

        $this->assertFalse($ogv->isAudio());
        $this->assertFalse($mp4->isAudio());
        $this->assertFalse($webm->isAudio());
    }

    /** @test */
    public function is_video_returns_false_for_audio_files()
    {
        $oga = new Image('http://example.com/thumb.oga.jpg', 100, 200, 'http://example.com/file.oga');
        $mp3 = new Image('http://example.com/thumb.mp3.jpg', 100, 200, 'http://example.com/file.mp3');
        $wav = new Image('http://example.com/thumb.wav.jpg', 100, 200, 'http://example.com/file.wav');

        $this->assertFalse($oga->isVideo());
        $this->assertFalse($mp3->isVideo());
        $this->assertFalse($wav->isVideo());
    }

    /** @test */
    public function ogg_file_would_be_recognized_as_audio_if_it_has_not_video_mime()
    {
        $ogg = new Image('http://example.com/thumb.ogg.jpg', 100, 200, 'http://example.com/file.ogg', 'right', 'desc', 'application/ogg');

        $this->assertTrue($ogg->isAudio());
        $this->assertFalse($ogg->isVideo());
    }

    /** @test */
    public function ogg_file_would_be_recognized_as_video_if_it_has_video_mime()
    {
        $ogg = new Image('http://example.com/thumb.ogg.jpg', 100, 200, 'http://example.com/file.ogg', 'right', 'desc', 'video/ogg');

        $this->assertFalse($ogg->isAudio());
        $this->assertTrue($ogg->isVideo());
    }

    /** @test */
    public function it_has_get_transcoded_mp3_url()
    {
        $oga = new Image('thumb-url', 100, 200, 'https://upload.wikimedia.org/wikipedia/commons/2/26/Filipp_Kirkorov_voice.oga');

        $this->assertEquals(
            'https://upload.wikimedia.org/wikipedia/commons/transcoded/2/26/Filipp_Kirkorov_voice.oga/Filipp_Kirkorov_voice.oga.mp3',
            $oga->getTranscodedMp3Url()
        );
    }

    /** @test */
    public function which_works_with_russian_file_names_too()
    {
        $oga = new Image('thumb-url', 100, 200, 'https://upload.wikimedia.org/wikipedia/ru/4/44/Филипп_Киркоров_-_Атлантида.ogg');

        $this->assertEquals(
            'https://upload.wikimedia.org/wikipedia/ru/transcoded/4/44/Филипп_Киркоров_-_Атлантида.ogg/Филипп_Киркоров_-_Атлантида.ogg.mp3',
            $oga->getTranscodedMp3Url()
        );
    }

    /** @test */
    public function which_returns_false_for_not_audio_files()
    {
        $webm = new Image('http://example.com/thumb.webm.jpg', 100, 200, 'http://example.com/file.webm');
        $this->assertFalse($webm->getTranscodedMp3Url());
    }

    /** @test */
    public function which_returns_false_for_already_mp3_files()
    {
        $mp3 = new Image('http://example.com/thumb.mp3.jpg', 100, 200, 'http://example.com/file.mp3');
        $this->assertFalse($mp3->getTranscodedMp3Url());
    }

    /** @test */
    public function and_it_will_return_false_for_not_wikimedia_urls()
    {
        $notWikipediaImage = new Image('thumb-url', 100, 200, 'https://example.com/wikipedia/commons/2/26/Filipp_Kirkorov_voice.oga');
        $this->assertFalse($notWikipediaImage->getTranscodedMp3Url());
    }
}
