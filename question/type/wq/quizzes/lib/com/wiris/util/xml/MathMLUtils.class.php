<?php

class com_wiris_util_xml_MathMLUtils {
	public function __construct() { 
	}
	static $contentTagsString = "ci@cn@apply@integers@reals@rationals@naturalnumbers@complexes@primes@exponentiale@imaginaryi@notanumber@true@false@emptyset@pi@eulergamma@infinity";
	static $contentTags;
	static $presentationTagsString = "mrow@mn@mi@mo@mfrac@mfenced@mroot@maction@mphantom@msqrt@mstyle@msub@msup@msubsup@munder@mover@munderover@menclose@mspace@mtext@ms";
	static $presentationTags;
	static function isPresentationMathML($mathml) {
		if(com_wiris_util_xml_MathMLUtils::$presentationTags === null) {
			com_wiris_util_xml_MathMLUtils::$presentationTags = _hx_explode("@", com_wiris_util_xml_MathMLUtils::$presentationTagsString);
		}
		return com_wiris_util_xml_MathMLUtils::isMathMLType($mathml, false, com_wiris_util_xml_MathMLUtils::$presentationTags);
	}
	static function isContentMathML($mathml) {
		if(com_wiris_util_xml_MathMLUtils::$contentTags === null) {
			com_wiris_util_xml_MathMLUtils::$contentTags = _hx_explode("@", com_wiris_util_xml_MathMLUtils::$contentTagsString);
		}
		return com_wiris_util_xml_MathMLUtils::isMathMLType($mathml, true, com_wiris_util_xml_MathMLUtils::$contentTags);
	}
	static function isMathMLType($mathml, $content, $tags) {
		$node = com_wiris_util_xml_WXmlUtils::parseXML($mathml);
		if($node->nodeType == Xml::$Document) {
			$node = $node->firstElement();
		}
		if($node->getNodeName() === "math") {
			$elements = $node->elements();
			if($elements->hasNext() && $elements->next() !== null && $elements->hasNext()) {
				return !$content;
			}
		}
		return com_wiris_util_xml_MathMLUtils::isMathMLTypeImpl($node, $tags);
	}
	static function isMathMLTypeImpl($node, $contentTags) {
		if($node->nodeType == Xml::$Element) {
			if($node->getNodeName() === "annotation-xml" || $node->getNodeName() === "annotation") {
				return false;
			}
			$i = $contentTags->iterator();
			while($i->hasNext()) {
				if($node->getNodeName() === $i->next()) {
					return true;
				}
			}
		}
		$j = $node->elements();
		while($j->hasNext()) {
			if(com_wiris_util_xml_MathMLUtils::isMathMLTypeImpl($j->next(), $contentTags)) {
				return true;
			}
		}
		return false;
	}
	static function isContentMathMLTag($tag) {
		return _hx_index_of(com_wiris_util_xml_MathMLUtils::$contentTagsString, $tag, null) !== -1;
	}
	static function removeStrokesAnnotation($mathml) {
		$start = null;
		$end = 0;
		while(($start = _hx_index_of($mathml, "<semantics>", $end)) !== -1) {
			$end = _hx_index_of($mathml, "</semantics>", $start);
			if($end === -1) {
				throw new HException("Error parsing semantics tag in MathML.");
			}
			$a = _hx_index_of($mathml, "<annotation encoding=\"application/json\">", $start);
			if($a !== -1 && $a < $end) {
				$b = _hx_index_of($mathml, "</annotation>", $a);
				if($b === -1 || $b >= $end) {
					throw new HException("Error parsing annotation tag in MathML.");
				}
				$b += 13;
				$mathml = _hx_substr($mathml, 0, $a) . _hx_substr($mathml, $b, null);
				$end -= $b - $a;
				$x = _hx_index_of($mathml, "<annotation", $start);
				if($x === -1 || $x > $end) {
					$mathml = _hx_substr($mathml, 0, $start) . _hx_substr($mathml, $start + 11, $end - ($start + 11)) . _hx_substr($mathml, $end + 12, null);
					$end -= 11;
				}
				unset($x,$b);
			}
			unset($a);
		}
		return $mathml;
	}
	function __toString() { return 'com.wiris.util.xml.MathMLUtils'; }
}
