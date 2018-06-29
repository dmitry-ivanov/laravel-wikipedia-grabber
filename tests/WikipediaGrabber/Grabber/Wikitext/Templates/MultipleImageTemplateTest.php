<?php

namespace Illuminated\Wikipedia\WikipediaGrabber\Tests\Grabber\Wikitext\Templates;

use Illuminated\Wikipedia\Grabber\Wikitext\Templates\MultipleImageTemplate;
use Illuminated\Wikipedia\WikipediaGrabber\Tests\TestCase;

class MultipleImageTemplateTest extends TestCase
{
    /** @test */
    public function it_can_extract_required_data_for_multiple_image_template()
    {
        $line = '{{multiple image | width = 60 |align = right | image1 = Yellow card.svg | alt1 = Yellow cartouche | image2 = Red card.svg | alt2 = Red cartouche | footer = Players are cautioned with a yellow card and sent off with a red card.}}';

        $this->assertEquals(
            '{{multiple image|width=60|right|image=Yellow card.svg|alt=Yellow cartouche|footer=Players are cautioned with a yellow card and sent off with a red card.}}',
            (new MultipleImageTemplate($line))->extract('Yellow_card.svg')
        );

        $this->assertEquals(
            '{{multiple image|width=60|right|image=Red card.svg|alt=Red cartouche|footer=Players are cautioned with a yellow card and sent off with a red card.}}',
            (new MultipleImageTemplate($line))->extract('Red_card.svg')
        );

        $this->assertEquals(
            '{{multiple image|width=60|right|footer=Players are cautioned with a yellow card and sent off with a red card.}}',
            (new MultipleImageTemplate($line))->extract('Fake image.svg')
        );
    }

    /** @test */
    public function it_can_extract_required_data_for_multiple_image_ru_template()
    {
        $line = '{{Кратное изображение |зона=left|подпись = Игроки предупреждаются… |ширина  = 60 |изобр1  = Yellow card.svg |изобр2  = Red card.svg}}';

        $this->assertEquals(
            '{{Кратное изображение|left|подпись=Игроки предупреждаются…|ширина=60|изобр=Yellow card.svg}}',
            (new MultipleImageTemplate($line))->extract('Yellow card.svg')
        );

        $this->assertEquals(
            '{{Кратное изображение|left|подпись=Игроки предупреждаются…|ширина=60|изобр=Red card.svg}}',
            (new MultipleImageTemplate($line))->extract('Red card.svg')
        );

        $this->assertEquals(
            '{{Кратное изображение|left|подпись=Игроки предупреждаются…|ширина=60}}',
            (new MultipleImageTemplate($line))->extract('Fake image.svg')
        );
    }

    /** @test */
    public function it_can_extract_required_data_for_image_column_ru_template()
    {
        $line = "{{Фотоколонка+ | align=left | Mammillaria prolifera20100407 076.jpg | текст1 = ''[[Mammillaria prolifera]]'' | Tussilago farfara20100409 07.jpg | текст2 = ''[[Tussilago farfara]]'' | Succisa pratensis20090811 088.jpg | текст3 = ''[[Succisa pratensis]]''}}";

        $this->assertEquals(
            '{{Фотоколонка+|left|Mammillaria prolifera20100407 076.jpg|текст=Mammillaria prolifera|Tussilago farfara20100409 07.jpg|Succisa pratensis20090811 088.jpg}}',
            (new MultipleImageTemplate($line))->extract('Mammillaria prolifera20100407 076.jpg')
        );

        $this->assertEquals(
            '{{Фотоколонка+|left|Mammillaria prolifera20100407 076.jpg|Tussilago farfara20100409 07.jpg|текст=Tussilago farfara|Succisa pratensis20090811 088.jpg}}',
            (new MultipleImageTemplate($line))->extract('Tussilago farfara20100409 07.jpg')
        );

        $this->assertEquals(
            '{{Фотоколонка+|left|Mammillaria prolifera20100407 076.jpg|Tussilago farfara20100409 07.jpg|Succisa pratensis20090811 088.jpg|текст=Succisa pratensis}}',
            (new MultipleImageTemplate($line))->extract('Succisa pratensis20090811 088.jpg')
        );
    }

    /** @test */
    public function it_can_extract_required_data_for_listen_template()
    {
        $line = "{{Listen | type = music | pos = right | Filename = Accordion chords-01.ogg | Title = Accordion chords | Description = Chords being played on an accordion | Filename2 = Moonlight.ogg | Title2 = ''Moonlight Sonata'' | Description2 = [[Ludwig van Beethoven|Beethoven]]'s [[Piano Sonata No. 14 (Beethoven)|Sonata in C-sharp minor]] | Filename3 = Brahms-waltz15.ogg | Title3 = Waltz No. 15 | Description3 = From [[Sixteen Waltzes, Op. 39 (Brahms)|Sixteen Waltzes, Op. 39]] by [[Johannes Brahms|Brahms]]}}";

        $this->assertEquals(
            '{{Listen|type=music|right|Filename=Accordion chords-01.ogg|Title=Accordion chords|Description=Chords being played on an accordion}}',
            (new MultipleImageTemplate($line))->extract('Accordion chords-01.ogg')
        );

        $this->assertEquals(
            "{{Listen|type=music|right|Filename=Moonlight.ogg|Title=Moonlight Sonata|Description=Beethoven's Sonata in C-sharp minor}}",
            (new MultipleImageTemplate($line))->extract('Moonlight.ogg')
        );

        $this->assertEquals(
            '{{Listen|type=music|right|Filename=Brahms-waltz15.ogg|Title=Waltz No. 15|Description=From Sixteen Waltzes, Op. 39 by Brahms}}',
            (new MultipleImageTemplate($line))->extract('Brahms-waltz15.ogg')
        );
    }

    /** @test */
    public function it_can_extract_required_data_for_listen_ru_template()
    {
        $line = '{{Listen | Имя файла = Russian Anthem chorus.ogg| float = left|Название = Гимн России| Описание = [[Гимн России]] | Имя файла2 = File2.ogg| Название2 = Файл 2| Описание2 = Описание 2 | Имя файла3 = File3.ogg| Название3 = Файл 3| Описание3 = Описание 3}}';

        $this->assertEquals(
            '{{Listen|Имя файла=Russian Anthem chorus.ogg|left|Название=Гимн России|Описание=Гимн России}}',
            (new MultipleImageTemplate($line))->extract('Russian Anthem chorus.ogg')
        );

        $this->assertEquals(
            '{{Listen|left|Имя файла=File2.ogg|Название=Файл 2|Описание=Описание 2}}',
            (new MultipleImageTemplate($line))->extract('File2.ogg')
        );

        $this->assertEquals(
            '{{Listen|left|Имя файла=File3.ogg|Название=Файл 3|Описание=Описание 3}}',
            (new MultipleImageTemplate($line))->extract('File3.ogg')
        );
    }
}
