<?php

use Illuminated\Wikipedia\Grabber\Partial\Section;

return [
    new Section('Page title', 'Some intro text here.', 1),
    new Section('Title 2 (2 level)', '', 2),
    new Section('Title 3 (3 level)', "Section 3 line 1.\nSection 3 line 2.", 3),
    new Section('Title 4 (3 level)', "Section 4 line 1.\n\nSection 4 line 2.", 3),
    new Section('Title 5 (3 level)', "Section 5 line 1.\nSection 5 line 2.\nSection 5 line 3.", 3),
    new Section('Title 6 (4 level)', 'Section 6 line 1.', 4),
    new Section('Title 7 (4 level)', "Section 7 line 1.\nSection 7 line 2.", 4),
    new Section('Title 8 (3 level)', "Section 8 line 1.\nSection 8 line 2.\nSection 8 line 3.", 3),
    new Section('Title 9 (2 level)', 'Section 9 line 1.', 2),
    new Section('Title 10 (2 level)', '', 2),
    new Section('Title 11 (3 level)', "Section 11 line 1.\nSection 11 line 2.\nSection 11 line 3.", 3),
];
