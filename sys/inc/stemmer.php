<?
class Lingua_Stem_Ru
{
var $VOWEL = '/аеиоуыэюя/uim';
var $PERFECTIVEGROUND = '/((ив|ивши|ившись|ыв|ывши|ывшись)|((?<=[ая])(в|вши|вшись)))$/uim';
var $REFLEXIVE = '/(с[яь])$/';
var $ADJECTIVE = '/(ее|ие|ые|ое|ими|ыми|ей|ий|ый|ой|ем|им|ым|ом|его|ого|еых|ую|юю|ая|яя|ою|ею)$/uim';
var $PARTICIPLE = '/((ивш|ывш|ующ)|((?<=[ая])(ем|нн|вш|ющ|щ)))$/uim';
var $VERB = '/((ила|ыла|ена|ейте|уйте|ите|или|ыли|ей|уй|ил|ыл|им|ым|ены|ить|ыть|ишь|ую|ю)|((?<=[ая])(ла|на|ете|йте|ли|й|л|ем|н|ло|но|ет|ют|ны|ть|ешь|нно)))$/uim';
var $NOUN = '/(а|ев|ов|ие|ье|е|иями|ями|ами|еи|ии|и|ией|ей|ой|ий|й|и|ы|ь|ию|ью|ю|ия|ья|я)$/uim';
var $RVRE = '/^(.*?[аеиоуыэюя])(.*)$/uim';
var $DERIVATIONAL = '/[^аеиоуыэюя][аеиоуыэюя]+[^аеиоуыэюя]+[аеиоуыэюя].*(?<=о)сть?$/uim';

function s(&$s, $re, $to)
{
$orig = $s;
$s = preg_replace($re, $to, $s);
return $orig !== $s;
}

function m($s, $re)
{
return preg_match($re, $s);
}

function stem_word($word)
{

$stem = $word;
do {
if (!preg_match($this->RVRE, $word, $p)) break;
$start = $p[1];
$RV = $p[2];
if (!$RV) break;

# Step 1
if (!$this->s($RV, $this->PERFECTIVEGROUND, '')) {
$this->s($RV, $this->REFLEXIVE, '');

if ($this->s($RV, $this->ADJECTIVE, '')) {
$this->s($RV, $this->PARTICIPLE, '');
} else {
if (!$this->s($RV, $this->VERB, ''))
$this->s($RV, $this->NOUN, '');
}
}

# Step 2
$this->s($RV, '/и$/uim', '');

# Step 3
if ($this->m($RV, $this->DERIVATIONAL))
$this->s($RV, '/ость?$/uim', '');

# Step 4
if (!$this->s($RV, '/ь$/uim', '')) {
$this->s($RV, '/ейше?/uim', '');
$this->s($RV, '/нн$/uim', 'н');
}

$stem = $start.$RV;
} while(false);

return $stem;

}


}
?>