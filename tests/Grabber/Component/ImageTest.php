<?php

namespace Illuminated\Wikipedia\Tests\Grabber\Component;

use Illuminated\Wikipedia\Grabber\Component\Image;
use Illuminated\Wikipedia\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class ImageTest extends TestCase
{
    #[Test]
    public function it_has_position_which_defaults_to_right(): void
    {
        $image = new Image('url', 100, 200, 'original');
        $this->assertEquals('right', $image->getPosition());
    }

    #[Test]
    public function position_can_be_set_to_left_value(): void
    {
        $image = new Image('url', 100, 200, 'original', 'left');
        $this->assertEquals('left', $image->getPosition());
    }

    #[Test]
    public function but_if_passed_position_is_unknown_it_would_be_set_to_right(): void
    {
        $image = new Image('url', 100, 200, 'original', 'foobar');
        $this->assertEquals('right', $image->getPosition());
    }

    #[Test]
    public function passed_mime_would_be_automatically_lowercase(): void
    {
        $image = new Image('url', 100, 200, 'original', 'left', 'description', 'IMAGE/JPEG');
        $this->assertEquals('image/jpeg', $image->getMime());
    }

    #[Test]
    public function it_has_get_alt_method_which_escapes_quotes_in_description(): void
    {
        $image = new Image('url', 100, 200, 'original', 'foobar', 'Description with single quote \' and double quote "!');

        $this->assertEquals('Description with single quote &#039; and double quote &quot;!', $image->getAlt());
    }

    #[Test]
    public function it_has_is_audio_method_which_returns_true_for_oga_and_mp3_and_wav_file_extensions(): void
    {
        $oga = new Image('http://example.com/thumb.oga.jpg', 100, 200, 'http://example.com/file.oga');
        $mp3 = new Image('http://example.com/thumb.mp3.jpg', 100, 200, 'http://example.com/file.mp3');
        $wav = new Image('http://example.com/thumb.wav.jpg', 100, 200, 'http://example.com/file.wav');

        $this->assertTrue($oga->isAudio());
        $this->assertTrue($mp3->isAudio());
        $this->assertTrue($wav->isAudio());
    }

    #[Test]
    public function and_is_audio_returns_false_for_jpg_and_other_image_extensions(): void
    {
        $jpg = new Image('http://example.com/file.thumb.jpg', 100, 200, 'http://example.com/file.jpg');
        $png = new Image('http://example.com/file.thumb.png', 100, 200, 'http://example.com/file.png');
        $gif = new Image('http://example.com/file.thumb.gif', 100, 200, 'http://example.com/file.gif');

        $this->assertFalse($jpg->isAudio());
        $this->assertFalse($png->isAudio());
        $this->assertFalse($gif->isAudio());
    }

    #[Test]
    public function it_has_is_video_method_which_returns_true_for_ogv_and_mp4_and_webm_file_extensions(): void
    {
        $ogv = new Image('http://example.com/thumb.ogv.jpg', 100, 200, 'http://example.com/file.ogv');
        $mp4 = new Image('http://example.com/thumb.mp4.jpg', 100, 200, 'http://example.com/file.mp4');
        $webm = new Image('http://example.com/thumb.webm.jpg', 100, 200, 'http://example.com/file.webm');

        $this->assertTrue($ogv->isVideo());
        $this->assertTrue($mp4->isVideo());
        $this->assertTrue($webm->isVideo());
    }

    #[Test]
    public function and_is_video_returns_false_for_jpg_and_other_image_extensions(): void
    {
        $jpg = new Image('http://example.com/file.thumb.jpg', 100, 200, 'http://example.com/file.jpg');
        $png = new Image('http://example.com/file.thumb.png', 100, 200, 'http://example.com/file.png');
        $gif = new Image('http://example.com/file.thumb.gif', 100, 200, 'http://example.com/file.gif');

        $this->assertFalse($jpg->isVideo());
        $this->assertFalse($png->isVideo());
        $this->assertFalse($gif->isVideo());
    }

    #[Test]
    public function is_audio_returns_false_for_video_files(): void
    {
        $ogv = new Image('http://example.com/thumb.ogv.jpg', 100, 200, 'http://example.com/file.ogv');
        $mp4 = new Image('http://example.com/thumb.mp4.jpg', 100, 200, 'http://example.com/file.mp4');
        $webm = new Image('http://example.com/thumb.webm.jpg', 100, 200, 'http://example.com/file.webm');

        $this->assertFalse($ogv->isAudio());
        $this->assertFalse($mp4->isAudio());
        $this->assertFalse($webm->isAudio());
    }

    #[Test]
    public function is_video_returns_false_for_audio_files(): void
    {
        $oga = new Image('http://example.com/thumb.oga.jpg', 100, 200, 'http://example.com/file.oga');
        $mp3 = new Image('http://example.com/thumb.mp3.jpg', 100, 200, 'http://example.com/file.mp3');
        $wav = new Image('http://example.com/thumb.wav.jpg', 100, 200, 'http://example.com/file.wav');

        $this->assertFalse($oga->isVideo());
        $this->assertFalse($mp3->isVideo());
        $this->assertFalse($wav->isVideo());
    }

    #[Test]
    public function ogg_file_would_be_recognized_as_audio_if_it_has_not_video_mime(): void
    {
        $ogg = new Image('http://example.com/thumb.ogg.jpg', 100, 200, 'http://example.com/file.ogg', 'right', 'desc', 'application/ogg');

        $this->assertTrue($ogg->isAudio());
        $this->assertFalse($ogg->isVideo());
    }

    #[Test]
    public function ogg_file_would_be_recognized_as_video_if_it_has_video_mime(): void
    {
        $ogg = new Image('http://example.com/thumb.ogg.jpg', 100, 200, 'http://example.com/file.ogg', 'right', 'desc', 'video/ogg');

        $this->assertFalse($ogg->isAudio());
        $this->assertTrue($ogg->isVideo());
    }

    #[Test]
    public function it_has_get_transcoded_mp3_url(): void
    {
        $oga = new Image('thumb-url', 100, 200, 'https://upload.wikimedia.org/wikipedia/commons/2/26/Filipp_Kirkorov_voice.oga');

        $this->assertEquals(
            'https://upload.wikimedia.org/wikipedia/commons/transcoded/2/26/Filipp_Kirkorov_voice.oga/Filipp_Kirkorov_voice.oga.mp3',
            $oga->getTranscodedMp3Url()
        );
    }

    #[Test]
    public function which_works_with_russian_file_names_too(): void
    {
        $oga = new Image('thumb-url', 100, 200, 'https://upload.wikimedia.org/wikipedia/ru/4/44/Филипп_Киркоров_-_Атлантида.ogg');

        $this->assertEquals(
            'https://upload.wikimedia.org/wikipedia/ru/transcoded/4/44/Филипп_Киркоров_-_Атлантида.ogg/Филипп_Киркоров_-_Атлантида.ogg.mp3',
            $oga->getTranscodedMp3Url()
        );
    }

    #[Test]
    public function which_returns_false_for_not_audio_files(): void
    {
        $webm = new Image('http://example.com/thumb.webm.jpg', 100, 200, 'http://example.com/file.webm');
        $this->assertFalse($webm->getTranscodedMp3Url());
    }

    #[Test]
    public function which_returns_false_for_already_mp3_files(): void
    {
        $mp3 = new Image('http://example.com/thumb.mp3.jpg', 100, 200, 'http://example.com/file.mp3');
        $this->assertFalse($mp3->getTranscodedMp3Url());
    }

    #[Test]
    public function and_it_will_return_false_for_not_wikimedia_urls(): void
    {
        $notWikipediaImage = new Image('thumb-url', 100, 200, 'https://example.com/wikipedia/commons/2/26/Filipp_Kirkorov_voice.oga');
        $this->assertFalse($notWikipediaImage->getTranscodedMp3Url());
    }

    #[Test]
    public function it_has_get_transcoded_webm_urls(): void
    {
        $ogv = new Image('thumb-url', 100, 200, 'https://upload.wikimedia.org/wikipedia/commons/2/26/Filipp_Kirkorov_voice.ogv');

        $this->assertEquals(
            collect([
                'https://upload.wikimedia.org/wikipedia/commons/transcoded/2/26/Filipp_Kirkorov_voice.ogv/Filipp_Kirkorov_voice.ogv.720p.webm',
                'https://upload.wikimedia.org/wikipedia/commons/transcoded/2/26/Filipp_Kirkorov_voice.ogv/Filipp_Kirkorov_voice.ogv.480p.webm',
                'https://upload.wikimedia.org/wikipedia/commons/transcoded/2/26/Filipp_Kirkorov_voice.ogv/Filipp_Kirkorov_voice.ogv.360p.webm',
                'https://upload.wikimedia.org/wikipedia/commons/transcoded/2/26/Filipp_Kirkorov_voice.ogv/Filipp_Kirkorov_voice.ogv.240p.webm',
                'https://upload.wikimedia.org/wikipedia/commons/transcoded/2/26/Filipp_Kirkorov_voice.ogv/Filipp_Kirkorov_voice.ogv.160p.webm',
            ]),
            $ogv->getTranscodedWebmUrls()
        );
    }

    #[Test]
    public function which_also_works_with_russian_file_names_too(): void
    {
        $ogv = new Image('thumb-url', 100, 200, 'https://upload.wikimedia.org/wikipedia/commons/2/26/Филипп_Киркоров_-_Атлантида.ogv');

        $this->assertEquals(
            collect([
                'https://upload.wikimedia.org/wikipedia/commons/transcoded/2/26/Филипп_Киркоров_-_Атлантида.ogv/Филипп_Киркоров_-_Атлантида.ogv.720p.webm',
                'https://upload.wikimedia.org/wikipedia/commons/transcoded/2/26/Филипп_Киркоров_-_Атлантида.ogv/Филипп_Киркоров_-_Атлантида.ogv.480p.webm',
                'https://upload.wikimedia.org/wikipedia/commons/transcoded/2/26/Филипп_Киркоров_-_Атлантида.ogv/Филипп_Киркоров_-_Атлантида.ogv.360p.webm',
                'https://upload.wikimedia.org/wikipedia/commons/transcoded/2/26/Филипп_Киркоров_-_Атлантида.ogv/Филипп_Киркоров_-_Атлантида.ogv.240p.webm',
                'https://upload.wikimedia.org/wikipedia/commons/transcoded/2/26/Филипп_Киркоров_-_Атлантида.ogv/Филипп_Киркоров_-_Атлантида.ogv.160p.webm',
            ]),
            $ogv->getTranscodedWebmUrls()
        );
    }

    #[Test]
    public function which_returns_false_for_not_video_files(): void
    {
        $oga = new Image('thumb-url', 100, 200, 'https://upload.wikimedia.org/wikipedia/commons/2/26/Filipp_Kirkorov_voice.oga');
        $this->assertFalse($oga->getTranscodedWebmUrls());
    }

    #[Test]
    public function which_works_for_already_webm_files(): void
    {
        $webm = new Image('thumb-url', 100, 200, 'https://upload.wikimedia.org/wikipedia/commons/2/26/Filipp_Kirkorov_voice.webm');

        $this->assertEquals(
            collect([
                'https://upload.wikimedia.org/wikipedia/commons/transcoded/2/26/Filipp_Kirkorov_voice.webm/Filipp_Kirkorov_voice.webm.720p.webm',
                'https://upload.wikimedia.org/wikipedia/commons/transcoded/2/26/Filipp_Kirkorov_voice.webm/Filipp_Kirkorov_voice.webm.480p.webm',
                'https://upload.wikimedia.org/wikipedia/commons/transcoded/2/26/Filipp_Kirkorov_voice.webm/Filipp_Kirkorov_voice.webm.360p.webm',
                'https://upload.wikimedia.org/wikipedia/commons/transcoded/2/26/Filipp_Kirkorov_voice.webm/Filipp_Kirkorov_voice.webm.240p.webm',
                'https://upload.wikimedia.org/wikipedia/commons/transcoded/2/26/Filipp_Kirkorov_voice.webm/Filipp_Kirkorov_voice.webm.160p.webm',
            ]),
            $webm->getTranscodedWebmUrls()
        );
    }

    #[Test]
    public function and_it_will_also_return_false_for_not_wikimedia_urls(): void
    {
        $notWikipediaImage = new Image('thumb-url', 100, 200, 'https://example.com/wikipedia/commons/2/26/Filipp_Kirkorov_voice.ogv');
        $this->assertFalse($notWikipediaImage->getTranscodedWebmUrls());
    }
}
