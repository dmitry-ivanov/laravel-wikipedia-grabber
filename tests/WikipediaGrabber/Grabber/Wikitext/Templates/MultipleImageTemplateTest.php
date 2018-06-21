<?php

namespace Illuminated\Wikipedia\WikipediaGrabber\Tests\Grabber\Wikitext\Templates;

use Illuminated\Wikipedia\Grabber\Wikitext\Templates\MultipleImageTemplate;
use Illuminated\Wikipedia\WikipediaGrabber\Tests\TestCase;

class MultipleImageTemplateTest extends TestCase
{
    /** @test */
    public function it_can_extract_required_data_for_multiple_image_template()
    {
        $line = '{{multiple image | width = 60 | image1 = Yellow card.svg | alt1 = Yellow cartouche | image2 = Red card.svg | alt2 = Red cartouche | footer = Players are cautioned with a yellow card and sent off with a red card.}}';

        $this->assertEquals(
            '{{multiple image|width=60|image=Yellow card.svg|alt=Yellow cartouche|footer=Players are cautioned with a yellow card and sent off with a red card.}}',
            (new MultipleImageTemplate($line))->extract('Yellow_card.svg')
        );

        $this->assertEquals(
            '{{multiple image|width=60|image=Red card.svg|alt=Red cartouche|footer=Players are cautioned with a yellow card and sent off with a red card.}}',
            (new MultipleImageTemplate($line))->extract('Red_card.svg')
        );

        $this->assertEquals(
            '{{multiple image|width=60|footer=Players are cautioned with a yellow card and sent off with a red card.}}',
            (new MultipleImageTemplate($line))->extract('Fake image.svg')
        );
    }

    /** @test */
    public function it_can_extract_required_data_for_multiple_image_ru_template()
    {
        $line = '{{Кратное изображение |подпись = Игроки предупреждаются… |ширина  = 60 |изобр1  = Yellow card.svg |изобр2  = Red card.svg}}';

        $this->assertEquals(
            '{{Кратное изображение|подпись=Игроки предупреждаются…|ширина=60|изобр=Yellow card.svg}}',
            (new MultipleImageTemplate($line))->extract('Yellow card.svg')
        );

        $this->assertEquals(
            '{{Кратное изображение|подпись=Игроки предупреждаются…|ширина=60|изобр=Red card.svg}}',
            (new MultipleImageTemplate($line))->extract('Red card.svg')
        );

        $this->assertEquals(
            '{{Кратное изображение|подпись=Игроки предупреждаются…|ширина=60}}',
            (new MultipleImageTemplate($line))->extract('Fake image.svg')
        );
    }

    /** @test */
    public function it_can_extract_required_data_for_image_column_ru_template()
    {
        $line = "{{Фотоколонка+|align=left|Mammillaria prolifera20100407 076.jpg|текст1=''[[Mammillaria prolifera]]''|Tussilago farfara20100409 07.jpg|текст2=''[[Tussilago farfara]]''|Succisa pratensis20090811 088.jpg|текст3=''[[Succisa pratensis]]''}}";

        $this->assertEquals(
            "{{Фотоколонка+|left|Mammillaria prolifera20100407 076.jpg|текст=Mammillaria prolifera|Tussilago farfara20100409 07.jpg|Succisa pratensis20090811 088.jpg}}",
            (new MultipleImageTemplate($line))->extract('Mammillaria prolifera20100407 076.jpg')
        );

        $this->assertEquals(
            "{{Фотоколонка+|left|Mammillaria prolifera20100407 076.jpg|Tussilago farfara20100409 07.jpg|текст=Tussilago farfara|Succisa pratensis20090811 088.jpg}}",
            (new MultipleImageTemplate($line))->extract('Tussilago farfara20100409 07.jpg')
        );

        $this->assertEquals(
            "{{Фотоколонка+|left|Mammillaria prolifera20100407 076.jpg|Tussilago farfara20100409 07.jpg|Succisa pratensis20090811 088.jpg|текст=Succisa pratensis}}",
            (new MultipleImageTemplate($line))->extract('Succisa pratensis20090811 088.jpg')
        );
    }
}
