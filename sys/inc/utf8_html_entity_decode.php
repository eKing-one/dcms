<?php
/**
 * Convert all HTML entities to UTF-8 characters
 * Функция декодирует гораздо больше именованных сущностей, чем стандартная html_entity_decode()
 * Все dec и hex сущности так же переводятся в UTF-8.
 *
 * @param    string   $s
 * @param    bool     $is_htmlspecialchars   обрабатывать специальные html сущности? (&lt; &gt; &amp; &quot;)
 * @return   string
 * @link     http://www.htmlhelp.com/reference/html40/entities/
 * @link     http://www.alanwood.net/demos/ent4_frame.html (HTML 4.01 Character Entity References)
 * @link     http://msdn.microsoft.com/workshop/author/dhtml/reference/charsets/charset1.asp?frame=true
 * @link     http://msdn.microsoft.com/workshop/author/dhtml/reference/charsets/charset2.asp?frame=true
 * @link     http://msdn.microsoft.com/workshop/author/dhtml/reference/charsets/charset3.asp?frame=true
 *
 * @author   Nasibullin Rinat <n a s i b u l l i n  at starlink ru>
 * @charset  ANSI
 * @version  2.1.12
 */
function utf8_html_entity_decode($s, $is_htmlspecialchars = false)
{
    #оптимизация скорости
    if (strlen($s) < 4  #по минимальной длине сущности - 4 байта: &#d; &xx;
        || ($pos = strpos($s, '&') === false) || strpos($s, ';', $pos) === false) return $s;
    $table = array(
      #Latin-1 Entities:
        '&nbsp;'   => "\xc2\xa0",  #no-break space = non-breaking space
        '&iexcl;'  => "\xc2\xa1",  #inverted exclamation mark
        '&cent;'   => "\xc2\xa2",  #cent sign
        '&pound;'  => "\xc2\xa3",  #pound sign
        '&curren;' => "\xc2\xa4",  #currency sign
        '&yen;'    => "\xc2\xa5",  #yen sign = yuan sign
        '&brvbar;' => "\xc2\xa6",  #broken bar = broken vertical bar
        '&sect;'   => "\xc2\xa7",  #section sign
        '&uml;'    => "\xc2\xa8",  #diaeresis = spacing diaeresis
        '&copy;'   => "\xc2\xa9",  #copyright sign
        '&ordf;'   => "\xc2\xaa",  #feminine ordinal indicator
        '&laquo;'  => "\xc2\xab",  #left-pointing double angle quotation mark = left pointing guillemet («)
        '&not;'    => "\xc2\xac",  #not sign
        '&shy;'    => "\xc2\xad",  #soft hyphen = discretionary hyphen
        '&reg;'    => "\xc2\xae",  #registered sign = registered trade mark sign
        '&macr;'   => "\xc2\xaf",  #macron = spacing macron = overline = APL overbar
        '&deg;'    => "\xc2\xb0",  #degree sign
        '&plusmn;' => "\xc2\xb1",  #plus-minus sign = plus-or-minus sign
        '&sup2;'   => "\xc2\xb2",  #superscript two = superscript digit two = squared
        '&sup3;'   => "\xc2\xb3",  #superscript three = superscript digit three = cubed
        '&acute;'  => "\xc2\xb4",  #acute accent = spacing acute
        '&micro;'  => "\xc2\xb5",  #micro sign
        '&para;'   => "\xc2\xb6",  #pilcrow sign = paragraph sign
        '&middot;' => "\xc2\xb7",  #middle dot = Georgian comma = Greek middle dot
        '&cedil;'  => "\xc2\xb8",  #cedilla = spacing cedilla
        '&sup1;'   => "\xc2\xb9",  #superscript one = superscript digit one
        '&ordm;'   => "\xc2\xba",  #masculine ordinal indicator
        '&raquo;'  => "\xc2\xbb",  #right-pointing double angle quotation mark = right pointing guillemet (»)
        '&frac14;' => "\xc2\xbc",  #vulgar fraction one quarter = fraction one quarter
        '&frac12;' => "\xc2\xbd",  #vulgar fraction one half = fraction one half
        '&frac34;' => "\xc2\xbe",  #vulgar fraction three quarters = fraction three quarters
        '&iquest;' => "\xc2\xbf",  #inverted question mark = turned question mark
      #Latin capital letter
        '&Agrave;' => "\xc3\x80",  #Latin capital letter A with grave = Latin capital letter A grave
        '&Aacute;' => "\xc3\x81",  #Latin capital letter A with acute
        '&Acirc;'  => "\xc3\x82",  #Latin capital letter A with circumflex
        '&Atilde;' => "\xc3\x83",  #Latin capital letter A with tilde
        '&Auml;'   => "\xc3\x84",  #Latin capital letter A with diaeresis
        '&Aring;'  => "\xc3\x85",  #Latin capital letter A with ring above = Latin capital letter A ring
        '&AElig;'  => "\xc3\x86",  #Latin capital letter AE = Latin capital ligature AE
        '&Ccedil;' => "\xc3\x87",  #Latin capital letter C with cedilla
        '&Egrave;' => "\xc3\x88",  #Latin capital letter E with grave
        '&Eacute;' => "\xc3\x89",  #Latin capital letter E with acute
        '&Ecirc;'  => "\xc3\x8a",  #Latin capital letter E with circumflex
        '&Euml;'   => "\xc3\x8b",  #Latin capital letter E with diaeresis
        '&Igrave;' => "\xc3\x8c",  #Latin capital letter I with grave
        '&Iacute;' => "\xc3\x8d",  #Latin capital letter I with acute
        '&Icirc;'  => "\xc3\x8e",  #Latin capital letter I with circumflex
        '&Iuml;'   => "\xc3\x8f",  #Latin capital letter I with diaeresis
        '&ETH;'    => "\xc3\x90",  #Latin capital letter ETH
        '&Ntilde;' => "\xc3\x91",  #Latin capital letter N with tilde
        '&Ograve;' => "\xc3\x92",  #Latin capital letter O with grave
        '&Oacute;' => "\xc3\x93",  #Latin capital letter O with acute
        '&Ocirc;'  => "\xc3\x94",  #Latin capital letter O with circumflex
        '&Otilde;' => "\xc3\x95",  #Latin capital letter O with tilde
        '&Ouml;'   => "\xc3\x96",  #Latin capital letter O with diaeresis
        '&times;'  => "\xc3\x97",  #multiplication sign
        '&Oslash;' => "\xc3\x98",  #Latin capital letter O with stroke = Latin capital letter O slash
        '&Ugrave;' => "\xc3\x99",  #Latin capital letter U with grave
        '&Uacute;' => "\xc3\x9a",  #Latin capital letter U with acute
        '&Ucirc;'  => "\xc3\x9b",  #Latin capital letter U with circumflex
        '&Uuml;'   => "\xc3\x9c",  #Latin capital letter U with diaeresis
        '&Yacute;' => "\xc3\x9d",  #Latin capital letter Y with acute
        '&THORN;'  => "\xc3\x9e",  #Latin capital letter THORN
      #Latin small letter
        '&szlig;'  => "\xc3\x9f",  #Latin small letter sharp s = ess-zed
        '&agrave;' => "\xc3\xa0",  #Latin small letter a with grave = Latin small letter a grave
        '&aacute;' => "\xc3\xa1",  #Latin small letter a with acute
        '&acirc;'  => "\xc3\xa2",  #Latin small letter a with circumflex
        '&atilde;' => "\xc3\xa3",  #Latin small letter a with tilde
        '&auml;'   => "\xc3\xa4",  #Latin small letter a with diaeresis
        '&aring;'  => "\xc3\xa5",  #Latin small letter a with ring above = Latin small letter a ring
        '&aelig;'  => "\xc3\xa6",  #Latin small letter ae = Latin small ligature ae
        '&ccedil;' => "\xc3\xa7",  #Latin small letter c with cedilla
        '&egrave;' => "\xc3\xa8",  #Latin small letter e with grave
        '&eacute;' => "\xc3\xa9",  #Latin small letter e with acute
        '&ecirc;'  => "\xc3\xaa",  #Latin small letter e with circumflex
        '&euml;'   => "\xc3\xab",  #Latin small letter e with diaeresis
        '&igrave;' => "\xc3\xac",  #Latin small letter i with grave
        '&iacute;' => "\xc3\xad",  #Latin small letter i with acute
        '&icirc;'  => "\xc3\xae",  #Latin small letter i with circumflex
        '&iuml;'   => "\xc3\xaf",  #Latin small letter i with diaeresis
        '&eth;'    => "\xc3\xb0",  #Latin small letter eth
        '&ntilde;' => "\xc3\xb1",  #Latin small letter n with tilde
        '&ograve;' => "\xc3\xb2",  #Latin small letter o with grave
        '&oacute;' => "\xc3\xb3",  #Latin small letter o with acute
        '&ocirc;'  => "\xc3\xb4",  #Latin small letter o with circumflex
        '&otilde;' => "\xc3\xb5",  #Latin small letter o with tilde
        '&ouml;'   => "\xc3\xb6",  #Latin small letter o with diaeresis
        '&divide;' => "\xc3\xb7",  #division sign
        '&oslash;' => "\xc3\xb8",  #Latin small letter o with stroke = Latin small letter o slash
        '&ugrave;' => "\xc3\xb9",  #Latin small letter u with grave
        '&uacute;' => "\xc3\xba",  #Latin small letter u with acute
        '&ucirc;'  => "\xc3\xbb",  #Latin small letter u with circumflex
        '&uuml;'   => "\xc3\xbc",  #Latin small letter u with diaeresis
        '&yacute;' => "\xc3\xbd",  #Latin small letter y with acute
        '&thorn;'  => "\xc3\xbe",  #Latin small letter thorn
        '&yuml;'   => "\xc3\xbf",  #Latin small letter y with diaeresis
      #Symbols and Greek Letters:
        '&fnof;'    => "\xc6\x92",  #Latin small f with hook = function = florin
        '&Alpha;'   => "\xce\x91",  #Greek capital letter alpha
        '&Beta;'    => "\xce\x92",  #Greek capital letter beta
        '&Gamma;'   => "\xce\x93",  #Greek capital letter gamma
        '&Delta;'   => "\xce\x94",  #Greek capital letter delta
        '&Epsilon;' => "\xce\x95",  #Greek capital letter epsilon
        '&Zeta;'    => "\xce\x96",  #Greek capital letter zeta
        '&Eta;'     => "\xce\x97",  #Greek capital letter eta
        '&Theta;'   => "\xce\x98",  #Greek capital letter theta
        '&Iota;'    => "\xce\x99",  #Greek capital letter iota
        '&Kappa;'   => "\xce\x9a",  #Greek capital letter kappa
        '&Lambda;'  => "\xce\x9b",  #Greek capital letter lambda
        '&Mu;'      => "\xce\x9c",  #Greek capital letter mu
        '&Nu;'      => "\xce\x9d",  #Greek capital letter nu
        '&Xi;'      => "\xce\x9e",  #Greek capital letter xi
        '&Omicron;' => "\xce\x9f",  #Greek capital letter omicron
        '&Pi;'      => "\xce\xa0",  #Greek capital letter pi
        '&Rho;'     => "\xce\xa1",  #Greek capital letter rho
        '&Sigma;'   => "\xce\xa3",  #Greek capital letter sigma
        '&Tau;'     => "\xce\xa4",  #Greek capital letter tau
        '&Upsilon;' => "\xce\xa5",  #Greek capital letter upsilon
        '&Phi;'     => "\xce\xa6",  #Greek capital letter phi
        '&Chi;'     => "\xce\xa7",  #Greek capital letter chi
        '&Psi;'     => "\xce\xa8",  #Greek capital letter psi
        '&Omega;'   => "\xce\xa9",  #Greek capital letter omega
        '&alpha;'   => "\xce\xb1",  #Greek small letter alpha
        '&beta;'    => "\xce\xb2",  #Greek small letter beta
        '&gamma;'   => "\xce\xb3",  #Greek small letter gamma
        '&delta;'   => "\xce\xb4",  #Greek small letter delta
        '&epsilon;' => "\xce\xb5",  #Greek small letter epsilon
        '&zeta;'    => "\xce\xb6",  #Greek small letter zeta
        '&eta;'     => "\xce\xb7",  #Greek small letter eta
        '&theta;'   => "\xce\xb8",  #Greek small letter theta
        '&iota;'    => "\xce\xb9",  #Greek small letter iota
        '&kappa;'   => "\xce\xba",  #Greek small letter kappa
        '&lambda;'  => "\xce\xbb",  #Greek small letter lambda
        '&mu;'      => "\xce\xbc",  #Greek small letter mu
        '&nu;'      => "\xce\xbd",  #Greek small letter nu
        '&xi;'      => "\xce\xbe",  #Greek small letter xi
        '&omicron;' => "\xce\xbf",  #Greek small letter omicron
        '&pi;'      => "\xcf\x80",  #Greek small letter pi
        '&rho;'     => "\xcf\x81",  #Greek small letter rho
        '&sigmaf;'  => "\xcf\x82",  #Greek small letter final sigma
        '&sigma;'   => "\xcf\x83",  #Greek small letter sigma
        '&tau;'     => "\xcf\x84",  #Greek small letter tau
        '&upsilon;' => "\xcf\x85",  #Greek small letter upsilon
        '&phi;'     => "\xcf\x86",  #Greek small letter phi
        '&chi;'     => "\xcf\x87",  #Greek small letter chi
        '&psi;'     => "\xcf\x88",  #Greek small letter psi
        '&omega;'   => "\xcf\x89",  #Greek small letter omega
        '&thetasym;'=> "\xcf\x91",  #Greek small letter theta symbol
        '&upsih;'   => "\xcf\x92",  #Greek upsilon with hook symbol
        '&piv;'     => "\xcf\x96",  #Greek pi symbol

        '&bull;'    => "\xe2\x80\xa2",  #bullet = black small circle
        '&hellip;'  => "\xe2\x80\xa6",  #horizontal ellipsis = three dot leader
        '&prime;'   => "\xe2\x80\xb2",  #prime = minutes = feet (для обозначения минут и футов)
        '&Prime;'   => "\xe2\x80\xb3",  #double prime = seconds = inches (для обозначения секунд и дюймов).
        '&oline;'   => "\xe2\x80\xbe",  #overline = spacing overscore
        '&frasl;'   => "\xe2\x81\x84",  #fraction slash
        '&weierp;'  => "\xe2\x84\x98",  #script capital P = power set = Weierstrass p
        '&image;'   => "\xe2\x84\x91",  #blackletter capital I = imaginary part
        '&real;'    => "\xe2\x84\x9c",  #blackletter capital R = real part symbol
        '&trade;'   => "\xe2\x84\xa2",  #trade mark sign
        '&alefsym;' => "\xe2\x84\xb5",  #alef symbol = first transfinite cardinal
        '&larr;'    => "\xe2\x86\x90",  #leftwards arrow
        '&uarr;'    => "\xe2\x86\x91",  #upwards arrow
        '&rarr;'    => "\xe2\x86\x92",  #rightwards arrow
        '&darr;'    => "\xe2\x86\x93",  #downwards arrow
        '&harr;'    => "\xe2\x86\x94",  #left right arrow
        '&crarr;'   => "\xe2\x86\xb5",  #downwards arrow with corner leftwards = carriage return
        '&lArr;'    => "\xe2\x87\x90",  #leftwards double arrow
        '&uArr;'    => "\xe2\x87\x91",  #upwards double arrow
        '&rArr;'    => "\xe2\x87\x92",  #rightwards double arrow
        '&dArr;'    => "\xe2\x87\x93",  #downwards double arrow
        '&hArr;'    => "\xe2\x87\x94",  #left right double arrow
        '&forall;'  => "\xe2\x88\x80",  #for all
        '&part;'    => "\xe2\x88\x82",  #partial differential
        '&exist;'   => "\xe2\x88\x83",  #there exists
        '&empty;'   => "\xe2\x88\x85",  #empty set = null set = diameter
        '&nabla;'   => "\xe2\x88\x87",  #nabla = backward difference
        '&isin;'    => "\xe2\x88\x88",  #element of
        '&notin;'   => "\xe2\x88\x89",  #not an element of
        '&ni;'      => "\xe2\x88\x8b",  #contains as member
        '&prod;'    => "\xe2\x88\x8f",  #n-ary product = product sign
        '&sum;'     => "\xe2\x88\x91",  #n-ary sumation
        '&minus;'   => "\xe2\x88\x92",  #minus sign
        '&lowast;'  => "\xe2\x88\x97",  #asterisk operator
        '&radic;'   => "\xe2\x88\x9a",  #square root = radical sign
        '&prop;'    => "\xe2\x88\x9d",  #proportional to
        '&infin;'   => "\xe2\x88\x9e",  #infinity
        '&ang;'     => "\xe2\x88\xa0",  #angle
        '&and;'     => "\xe2\x88\xa7",  #logical and = wedge
        '&or;'      => "\xe2\x88\xa8",  #logical or = vee
        '&cap;'     => "\xe2\x88\xa9",  #intersection = cap
        '&cup;'     => "\xe2\x88\xaa",  #union = cup
        '&int;'     => "\xe2\x88\xab",  #integral
        '&there4;'  => "\xe2\x88\xb4",  #therefore
        '&sim;'     => "\xe2\x88\xbc",  #tilde operator = varies with = similar to
        '&cong;'    => "\xe2\x89\x85",  #approximately equal to
        '&asymp;'   => "\xe2\x89\x88",  #almost equal to = asymptotic to
        '&ne;'      => "\xe2\x89\xa0",  #not equal to
        '&equiv;'   => "\xe2\x89\xa1",  #identical to
        '&le;'      => "\xe2\x89\xa4",  #less-than or equal to
        '&ge;'      => "\xe2\x89\xa5",  #greater-than or equal to
        '&sub;'     => "\xe2\x8a\x82",  #subset of
        '&sup;'     => "\xe2\x8a\x83",  #superset of
        '&nsub;'    => "\xe2\x8a\x84",  #not a subset of
        '&sube;'    => "\xe2\x8a\x86",  #subset of or equal to
        '&supe;'    => "\xe2\x8a\x87",  #superset of or equal to
        '&oplus;'   => "\xe2\x8a\x95",  #circled plus = direct sum
        '&otimes;'  => "\xe2\x8a\x97",  #circled times = vector product
        '&perp;'    => "\xe2\x8a\xa5",  #up tack = orthogonal to = perpendicular
        '&sdot;'    => "\xe2\x8b\x85",  #dot operator
        '&lceil;'   => "\xe2\x8c\x88",  #left ceiling = APL upstile
        '&rceil;'   => "\xe2\x8c\x89",  #right ceiling
        '&lfloor;'  => "\xe2\x8c\x8a",  #left floor = APL downstile
        '&rfloor;'  => "\xe2\x8c\x8b",  #right floor
        '&lang;'    => "\xe2\x8c\xa9",  #left-pointing angle bracket = bra
        '&rang;'    => "\xe2\x8c\xaa",  #right-pointing angle bracket = ket
        '&loz;'     => "\xe2\x97\x8a",  #lozenge
        '&spades;'  => "\xe2\x99\xa0",  #black spade suit
        '&clubs;'   => "\xe2\x99\xa3",  #black club suit = shamrock
        '&hearts;'  => "\xe2\x99\xa5",  #black heart suit = valentine
        '&diams;'   => "\xe2\x99\xa6",  #black diamond suit
      #Other Special Characters:
        '&OElig;'  => "\xc5\x92",  #Latin capital ligature OE
        '&oelig;'  => "\xc5\x93",  #Latin small ligature oe
        '&Scaron;' => "\xc5\xa0",  #Latin capital letter S with caron
        '&scaron;' => "\xc5\xa1",  #Latin small letter s with caron
        '&Yuml;'   => "\xc5\xb8",  #Latin capital letter Y with diaeresis
        '&circ;'   => "\xcb\x86",  #modifier letter circumflex accent
        '&tilde;'  => "\xcb\x9c",  #small tilde
        '&ensp;'   => "\xe2\x80\x82",  #en space
        '&emsp;'   => "\xe2\x80\x83",  #em space
        '&thinsp;' => "\xe2\x80\x89",  #thin space
        '&zwnj;'   => "\xe2\x80\x8c",  #zero width non-joiner
        '&zwj;'    => "\xe2\x80\x8d",  #zero width joiner
        '&lrm;'    => "\xe2\x80\x8e",  #left-to-right mark
        '&rlm;'    => "\xe2\x80\x8f",  #right-to-left mark
        '&ndash;'  => "\xe2\x80\x93",  #en dash
        '&mdash;'  => "\xe2\x80\x94",  #em dash
        '&lsquo;'  => "\xe2\x80\x98",  #left single quotation mark
        '&rsquo;'  => "\xe2\x80\x99",  #right single quotation mark (and apostrophe!)
        '&sbquo;'  => "\xe2\x80\x9a",  #single low-9 quotation mark
        '&ldquo;'  => "\xe2\x80\x9c",  #left double quotation mark
        '&rdquo;'  => "\xe2\x80\x9d",  #right double quotation mark
        '&bdquo;'  => "\xe2\x80\x9e",  #double low-9 quotation mark
        '&dagger;' => "\xe2\x80\xa0",  #dagger
        '&Dagger;' => "\xe2\x80\xa1",  #double dagger
        '&permil;' => "\xe2\x80\xb0",  #per mille sign
        '&lsaquo;' => "\xe2\x80\xb9",  #single left-pointing angle quotation mark
        '&rsaquo;' => "\xe2\x80\xba",  #single right-pointing angle quotation mark
        '&euro;'   => "\xe2\x82\xac",  #euro sign
    );
    $htmlspecialchars = array(
        '&quot;' => "\x22",  #quotation mark = APL quote (") &#34;
        '&amp;'  => "\x26",  #ampersand                  (&) &#38;
        '&lt;'   => "\x3c",  #less-than sign             (<) &#60;
        '&gt;'   => "\x3e",  #greater-than sign          (>) &#62;
    );

    if ($is_htmlspecialchars) $table += $htmlspecialchars;

    #заменяем именованные сущности:
    #оптимизация скорости: заменяем только те сущности, которые используются в html коде!
    #эта часть кода работает быстрее, чем $s = strtr($s, $table);
    preg_match_all('/&[a-zA-Z]+\d*;/s', $s, $m, null, $pos);
    foreach (array_unique($m[0]) as $entity)
    {
        if (array_key_exists($entity, $table)) $s = str_replace($entity, $table[$entity], $s);
    }#foreach

    if (($pos = strpos($s, '&#')) !== false)  #speed optimization
    {
        #заменяем числовые dec и hex сущности:
        $htmlspecialchars_flip = array_flip($htmlspecialchars);
        $s = preg_replace(
            '/&#((x)[\da-fA-F]{2,4}|\d{1,4});/se',
            '(array_key_exists($a = pack("C", $d = ("$2") ? hexdec("$1") : "$1"), $htmlspecialchars_flip) && ! $is_htmlspecialchars) ?
             $htmlspecialchars_flip[$a] :
             iconv("UCS-2BE", "UTF-8", pack("n", $d))', $s, -1, $pos);
    }
    return $s;
}

?>
