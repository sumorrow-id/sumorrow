<?php

/*
 * Achievement descriptions, keyed by the snake_cased English title stored in
 * the `achievements` table (see AchievementSeeder). Titles themselves stay in
 * English because AchievementService matches unlock rules on them.
 */

return [
    'first_summit' => 'Successfully logged your first mountain climbing activity.',
    'the_explorer' => 'Successfully conquered 5 different mountains.',
    'altitude_junkie' => 'Conquered 3 mountains above 3.000 MASL.',
    'endurance_master' => 'Completed a grueling expedition lasting 3 days or more.',
    'local_guide' => 'Wrote 5 detailed mountain reviews to help the community.',
    'visual_storyteller' => 'Shared 10 or more stunning photos of your climbing journeys.',
    'prepared_hiker' => 'Logged at least 10 essential items in your personal gear list.',
];
