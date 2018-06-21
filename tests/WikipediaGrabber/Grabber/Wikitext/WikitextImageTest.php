<?php

namespace Illuminated\Wikipedia\WikipediaGrabber\Tests\Grabber\Wikitext;

use Illuminated\Wikipedia\Grabber\Wikitext\WikitextImage;
use Illuminated\Wikipedia\WikipediaGrabber\Tests\TestCase;

class WikitextImageTest extends TestCase
{
    /** @test */
    public function it_can_parse_simple_image_wikitext()
    {
        $image = new WikitextImage('[[File:Name.jpg]]');

        $this->assertSame($image->getName(), 'File:Name.jpg');
        $this->assertSame($image->getType(), null);
        $this->assertSame($image->getBorder(), null);
        $this->assertSame($image->getLocation(), null);
        $this->assertSame($image->getAlignment(), null);
        $this->assertSame($image->getSize(), null);
        $this->assertSame($image->getLink(), null);
        $this->assertSame($image->getAlt(), null);
        $this->assertSame($image->getLangtag(), null);
        $this->assertSame($image->getPage(), null);
        $this->assertSame($image->getClass(), null);
        $this->assertSame($image->getCaption(), null);
    }

    /** @test */
    public function it_can_parse_image_wikitext_with_few_params()
    {
        $image = new WikitextImage('[[File:Name.jpg|thumb|left|200px]]');

        $this->assertSame($image->getName(), 'File:Name.jpg');
        $this->assertSame($image->getType(), 'thumb');
        $this->assertSame($image->getBorder(), null);
        $this->assertSame($image->getLocation(), 'left');
        $this->assertSame($image->getAlignment(), null);
        $this->assertSame($image->getSize(), '200px');
        $this->assertSame($image->getLink(), null);
        $this->assertSame($image->getAlt(), null);
        $this->assertSame($image->getLangtag(), null);
        $this->assertSame($image->getPage(), null);
        $this->assertSame($image->getClass(), null);
        $this->assertSame($image->getCaption(), null);
    }

    /** @test */
    public function params_can_be_mixed_in_any_order()
    {
        $image = new WikitextImage('[[File:Name.jpg|left|thumbnail=foo|border|upright|lang=foo|text-bottom|alt=foo|link=foo|Image Caption|page=11|class=foo|unknown parameter = foo bar baz]]');

        $this->assertSame($image->getName(), 'File:Name.jpg');
        $this->assertSame($image->getType(), 'thumbnail=foo');
        $this->assertSame($image->getBorder(), 'border');
        $this->assertSame($image->getLocation(), 'left');
        $this->assertSame($image->getAlignment(), 'text-bottom');
        $this->assertSame($image->getSize(), 'upright');
        $this->assertSame($image->getLink(), 'link=foo');
        $this->assertSame($image->getAlt(), 'alt=foo');
        $this->assertSame($image->getLangtag(), 'lang=foo');
        $this->assertSame($image->getPage(), 'page=11');
        $this->assertSame($image->getClass(), 'class=foo');
        $this->assertSame($image->getCaption(), 'Image Caption');
    }

    /** @test */
    public function caption_is_sanitized_against_formatting_links_and_templates()
    {
        $image = new WikitextImage("[[File:Name.jpg|right|frame|x200px|alt=foo|Image caption with [[Url|Link]] and {{nobr|Template with [[Another Link]]}} and '''Formatting with q'otes'''!]]");

        $this->assertSame($image->getName(), 'File:Name.jpg');
        $this->assertSame($image->getType(), 'frame');
        $this->assertSame($image->getBorder(), null);
        $this->assertSame($image->getLocation(), 'right');
        $this->assertSame($image->getAlignment(), null);
        $this->assertSame($image->getSize(), 'x200px');
        $this->assertSame($image->getLink(), null);
        $this->assertSame($image->getAlt(), 'alt=foo');
        $this->assertSame($image->getLangtag(), null);
        $this->assertSame($image->getPage(), null);
        $this->assertSame($image->getClass(), null);
        $this->assertSame($image->getCaption(), "Image caption with Link and Template with Another Link and Formatting with q'otes!");
    }

    /** @test */
    public function braces_are_optional_for_image_wikitext()
    {
        $image = new WikitextImage('File:Name.jpg|left|thumb|200x200px|alt=foo|Image caption with [[Url|Link]]');

        $this->assertSame($image->getName(), 'File:Name.jpg');
        $this->assertSame($image->getType(), 'thumb');
        $this->assertSame($image->getBorder(), null);
        $this->assertSame($image->getLocation(), 'left');
        $this->assertSame($image->getAlignment(), null);
        $this->assertSame($image->getSize(), '200x200px');
        $this->assertSame($image->getLink(), null);
        $this->assertSame($image->getAlt(), 'alt=foo');
        $this->assertSame($image->getLangtag(), null);
        $this->assertSame($image->getPage(), null);
        $this->assertSame($image->getClass(), null);
        $this->assertSame($image->getCaption(), 'Image caption with Link');
    }

    /** @test */
    public function it_ignores_parts_with_unknown_parameters()
    {
        $image = new WikitextImage('[[File:Name.jpg|none|thumb=foo|200 px|super|alt=foo|foo=bar|Image Caption|page=11]]');

        $this->assertSame($image->getName(), 'File:Name.jpg');
        $this->assertSame($image->getType(), 'thumb=foo');
        $this->assertSame($image->getBorder(), null);
        $this->assertSame($image->getLocation(), 'none');
        $this->assertSame($image->getAlignment(), 'super');
        $this->assertSame($image->getSize(), '200 px');
        $this->assertSame($image->getLink(), null);
        $this->assertSame($image->getAlt(), 'alt=foo');
        $this->assertSame($image->getLangtag(), null);
        $this->assertSame($image->getPage(), 'page=11');
        $this->assertSame($image->getClass(), null);
        $this->assertSame($image->getCaption(), 'Image Caption');
    }

    /** @test */
    public function it_has_get_description_method_which_returns_caption_by_default()
    {
        $image = new WikitextImage('[[File:Name.jpg|alt=Image Alt|Image Caption]]');
        $this->assertSame($image->getDescription(), 'Image Caption');
    }

    /** @test */
    public function which_will_return_alt_if_caption_is_empty()
    {
        $image = new WikitextImage('[[File:Name.jpg|alt=Image Alt]]');
        $this->assertSame($image->getDescription(), 'Image Alt');
    }

    /** @test */
    public function which_will_return_null_if_caption_and_alt_are_both_empty()
    {
        $image = new WikitextImage('[[File:Name.jpg]]');
        $this->assertSame($image->getDescription(), null);
    }

    /** @test */
    public function it_can_handle_ru_specific_wikitext_params_and_converts_them_to_en()
    {
        $image = new WikitextImage('[[Файл:Name.jpg|мини|справа|200пкс|альт=Альтернативный текст|Описание картинки|page=11]]');

        $this->assertSame($image->getName(), 'Файл:Name.jpg');
        $this->assertSame($image->getType(), 'thumb');
        $this->assertSame($image->getBorder(), null);
        $this->assertSame($image->getLocation(), 'right');
        $this->assertSame($image->getAlignment(), null);
        $this->assertSame($image->getSize(), '200пкс');
        $this->assertSame($image->getLink(), null);
        $this->assertSame($image->getAlt(), 'альт=Альтернативный текст');
        $this->assertSame($image->getLangtag(), null);
        $this->assertSame($image->getPage(), 'page=11');
        $this->assertSame($image->getClass(), null);
        $this->assertSame($image->getCaption(), 'Описание картинки');
    }

    /** @test */
    public function and_we_will_do_even_more_tests_for_that_ru_to_en_converting()
    {
        $image = new WikitextImage('[[Файл:Name.jpg|миниатюра|слева|100x200пкс|альт=Альтернативный текст|page=11|class=foo| unknown parameter = foo bar baz]]');

        $this->assertSame($image->getName(), 'Файл:Name.jpg');
        $this->assertSame($image->getType(), 'thumbnail');
        $this->assertSame($image->getBorder(), null);
        $this->assertSame($image->getLocation(), 'left');
        $this->assertSame($image->getAlignment(), null);
        $this->assertSame($image->getSize(), '100x200пкс');
        $this->assertSame($image->getLink(), null);
        $this->assertSame($image->getAlt(), 'альт=Альтернативный текст');
        $this->assertSame($image->getLangtag(), null);
        $this->assertSame($image->getCaption(), null);
        $this->assertSame($image->getPage(), 'page=11');
        $this->assertSame($image->getClass(), 'class=foo');
        $this->assertSame($image->getDescription(), 'Альтернативный текст');
    }

    /** @test */
    public function and_we_will_do_even_few_more_tests_for_that_ru_to_en_converting()
    {
        $image = new WikitextImage('[[Файл:Name.jpg|миниатюра|право|200 пкс|альт=Альтернативный текст]]');

        $this->assertSame($image->getName(), 'Файл:Name.jpg');
        $this->assertSame($image->getType(), 'thumbnail');
        $this->assertSame($image->getBorder(), null);
        $this->assertSame($image->getLocation(), 'right');
        $this->assertSame($image->getAlignment(), null);
        $this->assertSame($image->getSize(), '200 пкс');
        $this->assertSame($image->getLink(), null);
        $this->assertSame($image->getAlt(), 'альт=Альтернативный текст');
        $this->assertSame($image->getLangtag(), null);
        $this->assertSame($image->getCaption(), null);
        $this->assertSame($image->getPage(), null);
        $this->assertSame($image->getClass(), null);
        $this->assertSame($image->getDescription(), 'Альтернативный текст');
    }

    /** @test */
    public function and_we_will_do_the_last_one_test_for_that_ru_to_en_converting()
    {
        $image = new WikitextImage('[[Файл:Name.jpg|миниатюра|лево|x200пкс|альт=Альтернативный текст]]');

        $this->assertSame($image->getName(), 'Файл:Name.jpg');
        $this->assertSame($image->getType(), 'thumbnail');
        $this->assertSame($image->getBorder(), null);
        $this->assertSame($image->getLocation(), 'left');
        $this->assertSame($image->getAlignment(), null);
        $this->assertSame($image->getSize(), 'x200пкс');
        $this->assertSame($image->getLink(), null);
        $this->assertSame($image->getAlt(), 'альт=Альтернативный текст');
        $this->assertSame($image->getLangtag(), null);
        $this->assertSame($image->getCaption(), null);
        $this->assertSame($image->getPage(), null);
        $this->assertSame($image->getClass(), null);
        $this->assertSame($image->getDescription(), 'Альтернативный текст');
    }

    /** @test */
    public function it_can_parse_template_annotated_image_wikitext()
    {
        $image = new WikitextImage(
            '{{Annotated image|image=Mona Lisa color restoration2.jpg|image-width=2000|image-left=-850|image-top=-800|width=250|height=250|float=left|caption=Cropped Mona Lisa from a 2000 pixel image}}'
        );

        $this->assertSame($image->getCaption(), 'Cropped Mona Lisa from a 2000 pixel image');
    }

    /** @test */
    public function it_can_parse_template_annotated_image_ru_wikitext()
    {
        $image = new WikitextImage(
            '{{Описанное изображение|image=Mona Lisa color restoration2.jpg|image-width=2000|image-left=-850|image-top=-800|width=250|height=250|float=left|caption=Cropped Mona Lisa from a 2000 pixel image}}'
        );

        $this->assertSame($image->getCaption(), 'Cropped Mona Lisa from a 2000 pixel image');
    }

    /** @test */
    public function it_can_parse_template_css_image_crop_wikitext()
    {
        $image = new WikitextImage(
            '{{CSS Image crop|Image=Robert Lefèvre 001.jpg|Location=|Description=[[Летиция Рамолино]]. {{iw|Лефевр, Роберт|Лефевр}} (1813)|cWidth=150|oTop=9|oLeft=10}}'
        );

        $this->assertSame($image->getCaption(), 'Летиция Рамолино. Лефевр (1813)');
    }

    /** @test */
    public function it_can_parse_template_css_image_crop_ru_wikitext()
    {
        $image = new WikitextImage(
            '{{Часть Изображения|изобр=Robert Lefèvre 001.jpg|позиция=|Подпись=[[Летиция Рамолино]]. {{iw|Лефевр, Роберт|Лефевр}} (1813)|ширина=150|общая=168|верх=9}}'
        );

        $this->assertSame($image->getCaption(), 'Летиция Рамолино. Лефевр (1813)');
    }

    /** @test */
    public function it_can_parse_template_photo_row_ru_wikitext()
    {
        $image = new WikitextImage(
            '{{Фоторяд|Pushkin 04.jpg|С. Г. Чириков.jpg|A.S.Pushkin.jpg|Pushkin Alexander.jpg|ш1=150|ш2=140|ш3=137|ш4=143|Текст=Прижизненные портреты Пушкина работы [[Местр, Ксаверий Ксаверьевич|Ксавье де Местра]] (1800—1802), С. Г. Чирикова (1810), [[Тропинин, Василий Андреевич|В. А. Тропинина]] (1827), [[Соколов, Пётр Фёдорович|П. Ф. Соколова]] (1836)}}'
        );

        $this->assertSame($image->getCaption(), 'Прижизненные портреты Пушкина работы Ксавье де Местра (1800—1802), С. Г. Чирикова (1810), В. А. Тропинина (1827), П. Ф. Соколова (1836)');
    }

    /** @test */
    public function it_can_parse_template_photo_column_ru_wikitext()
    {
        $image = new WikitextImage(
            '{{Фотоколонка|ф1.jpg|ф2.jpg|ф3.jpg|ф4.jpg|ф5.jpg|ф5.jpg|ф5.jpg|ш=100|color=black|текст=Описание фото-колонки}}'
        );

        $this->assertSame($image->getCaption(), 'Описание фото-колонки');
    }

    /** @test */
    public function it_can_parse_template_multiple_image_wikitext()
    {
        $image = new WikitextImage(
            '{{multiple image|width=60|image1=Yellow card.svg|alt1=Yellow cartouche|image2=Red card.svg|alt2=Red cartouche|footer=Players are cautioned with a yellow card and sent off with a red card.}}'
        );

        $this->assertSame($image->getCaption(), 'Players are cautioned with a yellow card and sent off with a red card.');
    }

    /** @test */
    public function it_can_parse_template_multiple_image_ru_wikitext()
    {
        $image = new WikitextImage(
            '{{кратное изображение|зона=left|направление=horizontal|заголовок=|Подпись изображения=|ширина=138|изобр1=Vladimir Spiridonovich Putin.jpg|ширина1=|изобр2=Maria Ivanovna Shelomova.jpg|ширина2=|подпись=Родители Путина: Владимир Спиридонович Путин (1911—1999) и Мария Ивановна Путина (урождённая Шеломова) (1911—1998)}}'
        );

        $this->assertSame($image->getCaption(), 'Родители Путина: Владимир Спиридонович Путин (1911—1999) и Мария Ивановна Путина (урождённая Шеломова) (1911—1998)');
    }

    /** @test */
    public function it_can_parse_template_wide_image_wikitext()
    {
        $image = new WikitextImage(
            '{{wide image|Helsinki z00.jpg|1800px|[[Helsinki]] panorama|45%|none|alt=Panorama of city with mixture of five to ten story buildings}}'
        );

        $this->assertSame($image->getCaption(), 'Helsinki panorama');
    }

    /** @test */
    public function it_can_parse_template_wide_image_ru_wikitext()
    {
        $image = new WikitextImage(
            '{{Панорама|AlsterPanorama.jpg|900px|Панорама центральной части Гамбурга|text-align=center}}'
        );

        $this->assertSame($image->getCaption(), 'Панорама центральной части Гамбурга');
    }

    /** @test */
    public function it_can_parse_template_photo_montage_wikitext()
    {
        $image = new WikitextImage(
            '{{Photomontage|photo1a=Sevilla Plaza de España 19-03-2011 13-36-19.jpg|photo2a=Torredelorotyteatrolamaestranza.JPG|photo2b=Sevila10.JPG|photo3a=Alcaz archiv sev.jpg|text=Photo montage caption}}'
        );

        $this->assertSame($image->getCaption(), 'Photo montage caption');
    }

    /** @test */
    public function it_can_parse_template_photo_montage_ru_wikitext()
    {
        $image = new WikitextImage(
            '{{Фотомонтаж|photo1a=Sevilla Plaza de España 19-03-2011 13-36-19.jpg|photo2a=Torredelorotyteatrolamaestranza.JPG|photo2b=Sevila10.JPG|photo3a=Alcaz archiv sev.jpg|text=Описание фотомонтажа}}'
        );

        $this->assertSame($image->getCaption(), 'Описание фотомонтажа');
    }

    /** @test */
    public function it_can_parse_template_image_frame_wikitext()
    {
        $image = new WikitextImage(
            '{{Image frame|width=200|content=[[Image:PNG transparency demonstration 1.png|100px]][[Image:White Stars 3.svg|100px]]|caption=Example usage|link=Hello world|align=center}}'
        );

        $this->assertSame($image->getCaption(), 'Example usage');
    }

    /** @test */
    public function it_can_parse_template_image_frame_ru_wikitext()
    {
        $image = new WikitextImage(
            '{{Image frame|Содержание=[[Image:PNG transparency demonstration 1.png|100px]][[Image:White Stars 3.svg|100px]]|Заголовок=Пример использования|Заголовок сверху=1|Ссылка=Hello world}}'
        );

        $this->assertSame($image->getCaption(), 'Пример использования');
    }

    /** @test */
    public function it_can_parse_template_image_frame_ru2_wikitext()
    {
        $image = new WikitextImage(
            '{{Рамка в стиле миниатюры|Содержание=[[Image:PNG transparency demonstration 1.png|100px]][[Image:White Stars 3.svg|100px]]|Заголовок=Пример использования|Заголовок сверху=1|Ссылка=Hello world}}'
        );

        $this->assertSame($image->getCaption(), 'Пример использования');
    }

    /** @test */
    public function it_can_parse_template_listen_wikitext()
    {
        $image = new WikitextImage(
            '{{Listen|header=Recordings of this phrase:|type=speech|filename=Frase de Neil Armstrong.ogg|title="One small step for a man..."|description=First words spoken on the [[Moon]].}}'
        );

        $this->assertSame($image->getCaption(), 'First words spoken on the Moon.');
    }

    /** @test */
    public function it_can_parse_template_listen_ru_wikitext()
    {
        $image = new WikitextImage('{{Listen|Имя_файла=Russian Anthem chorus.ogg|Название=Гимн России|Описание=[[Гимн России]]}}');
        $this->assertSame($image->getCaption(), 'Гимн России');
    }

    /** @test */
    public function it_can_parse_template_audio_wikitext()
    {
        $image = new WikitextImage('{{Audio|en-us-Alabama.ogg|pronunciation of "Alabama"|help=no}}');
        $this->assertSame($image->getCaption(), 'pronunciation of "Alabama"');
    }

    /** @test */
    public function it_can_parse_template_pronunciation_wikitext()
    {
        $image = new WikitextImage('{{pronunciation|Nl-be guy verhofstadt.ogg|Dutch pronunciation|help=no}}');
        $this->assertSame($image->getCaption(), 'Dutch pronunciation');
    }

    /** @test */
    public function it_can_parse_template_sample_wikitext()
    {
        $image = new WikitextImage('{{Sample|название=«Lucky Star» (1984)|файл=Madonna-lucky star.ogg|формат=[[Ogg Vorbis]], 29 с, 62 Кб/с|пояснения=Четвёртый сингл «[[Lucky Star (песня Мадонны)|Lucky Star]]» с дебютного альбома занял 4-е место в чарте [[Billboard Hot 100]] и стал первым хитом Мадонны, попавшим в «первую пятёрку»{{cite web|url=http://www.billboard.com/music/madonna/chart-history/hot-100/song/333472|title=Madonna Chart History - Lucky Star - Hot 100|publisher=Billboard|lang=en|accessdate=2017-10-07}}. Этот сингл был издан повторно в том же 1984 году после попадания пятого сингла «[[Borderline (песня Мадонны)|Borderline]]» в «первую десятку»{{cite web|url=http://reggielucas.com/index.php/awards|title=Hits, Awards and Milestones in Reggie Lucas\'s Career|lang=en|publisher=Reggielucal.com|accessdate=2017-12-05}}{{cite news|title = The Ultimate Ranking Of Pop Stardom|url = http://time.com/music-ranking|website = Time|accessdate=March 10, 2016|lang=en}}).}}');
        $this->assertSame($image->getCaption(), 'Четвёртый сингл «Lucky Star» с дебютного альбома занял 4-е место в чарте Billboard Hot 100 и стал первым хитом Мадонны, попавшим в «первую пятёрку». Этот сингл был издан повторно в том же 1984 году после попадания пятого сингла «Borderline» в «первую десятку»).');
    }

    /** @test */
    public function it_can_parse_template_sample_ru1_wikitext()
    {
        $image = new WikitextImage('{{Музыкальный отрывок стиля|название=«Lucky Star» (1984)|файл=Madonna-lucky star.ogg|формат=[[Ogg Vorbis]], 29 с, 62 Кб/с|пояснения=Четвёртый сингл «[[Lucky Star (песня Мадонны)|Lucky Star]]» с дебютного альбома занял 4-е место в чарте [[Billboard Hot 100]] и стал первым хитом Мадонны, попавшим в «первую пятёрку»{{cite web|url=http://www.billboard.com/music/madonna/chart-history/hot-100/song/333472|title=Madonna Chart History - Lucky Star - Hot 100|publisher=Billboard|lang=en|accessdate=2017-10-07}}. Этот сингл был издан повторно в том же 1984 году после попадания пятого сингла «[[Borderline (песня Мадонны)|Borderline]]» в «первую десятку»{{cite web|url=http://reggielucas.com/index.php/awards|title=Hits, Awards and Milestones in Reggie Lucas\'s Career|lang=en|publisher=Reggielucal.com|accessdate=2017-12-05}}{{cite news|title = The Ultimate Ranking Of Pop Stardom|url = http://time.com/music-ranking|website = Time|accessdate=March 10, 2016|lang=en}}).}}');
        $this->assertSame($image->getCaption(), 'Четвёртый сингл «Lucky Star» с дебютного альбома занял 4-е место в чарте Billboard Hot 100 и стал первым хитом Мадонны, попавшим в «первую пятёрку». Этот сингл был издан повторно в том же 1984 году после попадания пятого сингла «Borderline» в «первую десятку»).');
    }

    /** @test */
    public function it_can_parse_template_sample_ru2_wikitext()
    {
        $image = new WikitextImage('{{Семпл|название=«Lucky Star» (1984)|файл=Madonna-lucky star.ogg|формат=[[Ogg Vorbis]], 29 с, 62 Кб/с|пояснения=Четвёртый сингл «[[Lucky Star (песня Мадонны)|Lucky Star]]» с дебютного альбома занял 4-е место в чарте [[Billboard Hot 100]] и стал первым хитом Мадонны, попавшим в «первую пятёрку»{{cite web|url=http://www.billboard.com/music/madonna/chart-history/hot-100/song/333472|title=Madonna Chart History - Lucky Star - Hot 100|publisher=Billboard|lang=en|accessdate=2017-10-07}}. Этот сингл был издан повторно в том же 1984 году после попадания пятого сингла «[[Borderline (песня Мадонны)|Borderline]]» в «первую десятку»{{cite web|url=http://reggielucas.com/index.php/awards|title=Hits, Awards and Milestones in Reggie Lucas\'s Career|lang=en|publisher=Reggielucal.com|accessdate=2017-12-05}}{{cite news|title = The Ultimate Ranking Of Pop Stardom|url = http://time.com/music-ranking|website = Time|accessdate=March 10, 2016|lang=en}}).}}');
        $this->assertSame($image->getCaption(), 'Четвёртый сингл «Lucky Star» с дебютного альбома занял 4-е место в чарте Billboard Hot 100 и стал первым хитом Мадонны, попавшим в «первую пятёрку». Этот сингл был издан повторно в том же 1984 году после попадания пятого сингла «Borderline» в «первую десятку»).');
    }

    /** @test */
    public function it_can_parse_template_sample_ru3_wikitext()
    {
        $image = new WikitextImage('{{МузОС|название=«Lucky Star» (1984)|файл=Madonna-lucky star.ogg|формат=[[Ogg Vorbis]], 29 с, 62 Кб/с|пояснения=Четвёртый сингл «[[Lucky Star (песня Мадонны)|Lucky Star]]» с дебютного альбома занял 4-е место в чарте [[Billboard Hot 100]] и стал первым хитом Мадонны, попавшим в «первую пятёрку»{{cite web|url=http://www.billboard.com/music/madonna/chart-history/hot-100/song/333472|title=Madonna Chart History - Lucky Star - Hot 100|publisher=Billboard|lang=en|accessdate=2017-10-07}}. Этот сингл был издан повторно в том же 1984 году после попадания пятого сингла «[[Borderline (песня Мадонны)|Borderline]]» в «первую десятку»{{cite web|url=http://reggielucas.com/index.php/awards|title=Hits, Awards and Milestones in Reggie Lucas\'s Career|lang=en|publisher=Reggielucal.com|accessdate=2017-12-05}}{{cite news|title = The Ultimate Ranking Of Pop Stardom|url = http://time.com/music-ranking|website = Time|accessdate=March 10, 2016|lang=en}}).}}');
        $this->assertSame($image->getCaption(), 'Четвёртый сингл «Lucky Star» с дебютного альбома занял 4-е место в чарте Billboard Hot 100 и стал первым хитом Мадонны, попавшим в «первую пятёрку». Этот сингл был издан повторно в том же 1984 году после попадания пятого сингла «Borderline» в «первую десятку»).');
    }
}
