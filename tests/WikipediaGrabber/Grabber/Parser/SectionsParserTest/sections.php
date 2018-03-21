<?php

use Illuminated\Wikipedia\Grabber\Component\Section;

return [
    new Section('Page title', 'Some intro text here.', 1),
    new Section('Title 2 (2 level)', '', 2),
    new Section('Title 2-1 (3 level)', "Section 2-1 line 1.\nSection 2-1 line 2.", 3),
    new Section('Title 2-2 (3 level)', "Section 2-2 line 1.\n\nSection 2-2 line 2.", 3),
    new Section('Title 2-3 (3 level)', "Section 2-3 line 1.\nSection 2-3 line 2.\nSection 2-3 line 3.", 3),
    new Section('Title 2-3-1 (4 level)', 'Section 2-3-1 line 1.', 4),
    new Section('Title 2-3-2 (4 level)', "Section 2-3-2 line 1.\nSection 2-3-2 line 2.", 4),
    new Section('Title 2-4 (3 level)', "Section 2-4 line 1.\nSection 2-4 line 2.\nSection 2-4 line 3.", 3),
    new Section('Title 3 (2 level)', 'Section 3 line 1.', 2),
    new Section('Title 4 (2 level)', '', 2),
    new Section('Title 4-1 (3 level)', "Section 4-1 line 1.\nSection 4-1 line 2.\nSection 4-1 line 3.", 3),
];
