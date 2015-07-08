This has a very specialized use, but if you need to extract names and genders from a short biographical text, try it out.

````
<?php
   $nameFinder = new Names($my_text);
   $name = $nameFinder->guessName();
   $gender = $nameFinder->guessGender();
?>
````

It guesses purely based on number of occurrences and honorifics/pronouns/gender-nouns, so it will fail to perfom in many obvious cases. There are some better methods for dealing with this issue discussed in papers such as:

1. Extracting Names from Natural-Language Text (Y. Ravin, 1997)
1. Disambiguation of proper names in text (Y. Ravin, N. Wacholder, 1997)
1. Extracting company names from text (L.F. Rau, 1991)
1. Extracting personal names from email: Applying named entity recognition to informal text (E. Minkov, R.C. Wang, 2005)

