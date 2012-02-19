<?php
   /*
    *    libnames::Names - a class used to extract natural names and genders
    *    from particular English language text. Very ethnocentric, as it relies
    *    largely on European notions of what a name is. 
    *    
    *    The code here is largely specialized, it relies on the body of text
    *    referring to a single person (though it may have multiple names in it)
    *    such as a short biography or an article about a particular person. It is 
    *    finicky and does not work perfectly. 
    *
    *    Does not rely on any of the more advanced NLP techniques for extracting
    *    proper nouns. See some of the IBM research in that area at:
    *       Extracting Names from Natural-Language Text (Y. Ravin, 1997)
    *       Disambiguation of proper names in text (Y. Ravin, N. Wacholder, 1997)
    *       Extracting company names from text (L.F. Rau, 1991)
    *       Extracting personal names from email: Applying named entity recognition to informal text (E. Minkov, R.C. Wang, 2005)
    *
    */

   class Names {
      private $content;

      private $pronouns;
      private $honorifics;

      private $results_index = 0;
      private $results;

      const NAME_REG = "\b([A-Z][a-z'-]+) ([A-Z][a-z'-]+ )*(([A-Z][a-z'-]+)|(Mac|Mc|O\'|M\')[A-Z][a-z'-]+)\b";
      
      const NG_FOUND = 0;
      const NG_SOUND = 2;
      const NG_LEVEN = 4;

      public function __construct($content) { 
         $this->setupContent($content);
         $this->setupData();
      }

      public function guessName() {
         if(!isset($this->results)) {
            preg_match_all("/" . Names::NAME_REG . "/", $this->content, $results);
            $possible_names = $results[0];

            $this->results = array();
            $content = $this->removeStopWords(strtolower($this->content));
            foreach($possible_names as $name) {
               $name_parts = explode(" ", $name);
               $first = $name_parts[0];
               $last = end($name_parts);
               $firstlast = $first . " " . $last;

               $counts = array();
               foreach(array("name", "firstlast", "first", "last") as $part) {
                  $name_part = strtolower($$part);
                  $counts[$part] = substr_count($content, $name_part);
               }

               $total_count = $counts['first'] + $counts['last']; // - $counts['firstlast'] - $counts['name']; // allow double counts as a weight

               $this->results[] = array("first"=>$first, "last"=>$last, "whole"=>$name, "count"=>$total_count);
            }

            usort($this->results, create_function('$a, $b', 'return $b["count"] - $a["count"];'));
         }
      
         if($this->results_index > count($this->results)) {
            return null; 
         } else {
            return $this->results[$this->results_index++];
         }
      }

      public function guessGender() {
         $content = strtolower($this->content);
         
         $counts = array();
         foreach(array("pronouns", "honorifics") as $data) {
            $words = $this->$data;

            foreach($genders as $gender => $words) {
               $counts[$gender] = 0;

               foreach($words as $word) 
               {
                  $counts[$gender] += substr_count($content, $word . " ");
               }
            }
         }

         return $this->counts;
      }

      public function guessNameGender($name) {
         die("Not implemented.");

         // look up name in gender database.
         // if it doesn't exist, soundex it, and look again.
         // if it STILL doesn't exist, soundex and return closest levenshtein
         
         return array($guess, $method);
      }
   
      private function removeStopWords($text) {
         $stopWords = $this->loadArrayFile('data/stopwords');
         $stopWords = array_map("preg_quote", $stopWords);
         $stopWords = rtrim(implode("|", $stopWords), "|");
         $stopWords = "(" . $stopWords . ")";
         return preg_replace("/\b" . $stopWords . "\b/i", "", $text);
      }

      private function setupContent($content) {
         $this->content = trim($content);
      }

      private function setupData() {
         $this->pronouns['m'] = $this->loadArrayFile("data/pronouns_m"); 
         $this->pronouns['f'] = $this->loadArrayFile("data/pronouns_f");

         $this->honorifics['m'] = $this->loadArrayFile("data/honorifics_m");
         $this->honorifics['f'] = $this->loadArrayFile("data/honorifics_f");
      }

      private function loadArrayFile($file) {
         return explode("\n", file_get_contents($file));
      }
   }
