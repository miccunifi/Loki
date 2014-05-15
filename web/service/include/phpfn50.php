<?php

/**
 * PHPMaker functions and classes
 * (C) 2002-2007 e.World Technology Limited. All rights reserved.
*/

/**
 * Functions to init arrays
 */

function ew_InitArray($iLen, $vValue) {
	if (function_exists('array_fill')) { // PHP 4 >= 4.2.0,
		return array_fill(0, $iLen, $vValue);
	} else {
		$aResult = array();
		for ($iCount = 0; $iCount < $iLen; $iCount++)
			$aResult[] = $vValue;
		return $aResult;
	}
}

function ew_Init2DArray($iLen1, $iLen2, $vValue) {
	return ew_InitArray($iLen1, ew_InitArray($iLen2, $vValue));
}

/**
 * Functions for converting encoding
 */

function ew_ConvertToUtf8($str) {
	return ew_Convert(EW_ENCODING, "UTF-8", $str);
}

function ew_ConvertFromUtf8($str) {
	return ew_Convert("UTF-8", EW_ENCODING, $str);
}

function ew_Convert($from, $to, $str)
{
	if ($from != "" && $to != "" && $from != $to) {
		if (function_exists("iconv")) {
			return iconv($from, $to, $str);
		} elseif (function_exists("mb_convert_encoding")) {
			return mb_convert_encoding($str, $to, $from);
		} else {
			return $str;
		}
	} else {
	return $str;
	}
}

/**
 * XML document class
 */

class cXMLDocument {
	var $Encoding = EW_XML_ENCODING;
	var $RootTagName  = 'table';
	var $RowTagName = 'row';
	var $XmlDoc;
	var $XmlTbl;
	var $XmlRow;
	var $XML = '';
	var $NullValue = 'NULL';

	function cXMLDocument() {
		if (EW_IS_PHP5) {
			$this->XmlDoc = new DOMDocument("1.0", $this->Encoding);
			$this->XmlTbl = $this->XmlDoc->createElement($this->RootTagName);
			$this->XmlDoc->appendChild($this->XmlTbl);
		}
	}

	function BeginRow() {
		if (EW_IS_PHP5) {
			$this->XmlRow = $this->XmlDoc->createElement($this->RowTagName);
			$this->XmlTbl->appendChild($this->XmlRow);
		} else {
			$this->XML .= "<$this->RowTagName>";
		}
	}

	function EndRow() {
		if (!EW_IS_PHP5) {
			$this->XML .= "</$this->RowTagName>";
		}
	}

	function AddField($name, $value) {
		if (is_null($value)) $value = $this->NullValue;
		if (EW_IS_PHP5) {
			$value = ew_ConvertToUtf8($value); // Convert to UTF-8
			$xmlfld = $this->XmlDoc->createElement($name);
			$this->XmlRow->appendChild($xmlfld);
			$xmlfld->appendChild($this->XmlDoc->createTextNode($value));
		} else {
			$value = ew_Convert(EW_ENCODING, EW_XML_ENCODING, $value); // Convert to output encoding
			$this->XML .= "<$name>" . htmlspecialchars($value) . "</$name>";
		}
	}

	function XML() {
		if (EW_IS_PHP5) {
			return $this->XmlDoc->saveXML();
		} else {
			return "<?xml version=\"1.0\"". (($this->Encoding <> "") ? " encoding=\"$this->Encoding\"" : "") .
				" ?>\n<$this->RootTagName>$this->XML</$this->RootTagName>";
		}
	}
}

/**
 * QueryString class
 */

class cQueryString {
	var $values = array();
	var $Count;

	function cQueryString() {
		$ar = explode("&", ew_ServerVar("QUERY_STRING"));
		foreach ($ar as $p) {
			$arp = explode("=", $p);
			if (count($arp) == 2) $this->values[urldecode($arp[0])] = $arp[1];
		}
		$this->Count = count($this->values);
	}

	function getValue($name) {
		return (array_key_exists($name, $this->values)) ? $this->values[$name] : "";
	}

	function getUrlDecodedValue($name) {
		return urldecode($this->getValue($name));
	}

	function getRawUrlDecodedValue($name) {
		return rawurldecode($this->getValue($name));
	}

	function getConvertedValue($name) {
		return ew_ConvertFromUtf8($this->getRawUrlDecodedValue($name));
	}
}

/**
 * Email class
 */

class cEmail {

	// Class properties
	var $Sender; // Sender
	var $Recipient; // Recipient
	var $Cc; // Cc
	var $Bcc; // Bcc
	var $Subject; // Subject
	var $Format; // Format
	var $Content; // Content

	function cEmail() {
		$this->Sender = "";
		$this->Recipient = "";
		$this->Cc = "";
		$this->Bcc = "";
		$this->Subject = "";
		$this->Format = "";
		$this->Content = "";
	}

	// Method to load email from template
	function Load($fn) {
		$fn = realpath(".") . EW_PATH_DELIMITER . $fn;
		$sWrk = ew_ReadFile($fn); // Load text file content
		if ($sWrk <> "") {

			// Locate Header & Mail Content
			if (EW_IS_WINDOWS) {
				$i = strpos($sWrk, "\r\n\r\n");
			} else {
				$i = strpos($sWrk, "\n\n");
				if ($i === FALSE) $i = strpos($sWrk, "\r\n\r\n");
			}
			if ($i > 0) {
				$sHeader = substr($sWrk, 0, $i);
				$this->Content = trim(substr($sWrk, $i, strlen($sWrk)));
				if (EW_IS_WINDOWS) {
					$arrHeader = explode("\r\n", $sHeader);
				} else {
					$arrHeader = explode("\n", $sHeader);
				}
				for ($j = 0; $j < count($arrHeader); $j++) {
					$i = strpos($arrHeader[$j], ":");
					if ($i > 0) {
						$sName = trim(substr($arrHeader[$j], 0, $i));
						$sValue = trim(substr($arrHeader[$j], $i+1, strlen($arrHeader[$j])));
						switch (strtolower($sName))
						{
							case "subject":
								$this->Subject = $sValue;
								break;
							case "from":
								$this->Sender = $sValue;
								break;
							case "to":
								$this->Recipient = $sValue;
								break;
							case "cc":
								$this->Cc = $sValue;
								break;
							case "bcc":
								$this->Bcc = $sValue;
								break;
							case "format":
								$this->Format = $sValue;
								break;
						}
					}
				}
			}
		}
	}

	// Method to replace sender
	function ReplaceSender($ASender) {
		$this->Sender = str_replace('<!--$From-->', $ASender, $this->Sender);
	}

	// Method to replace recipient
	function ReplaceRecipient($ARecipient) {
		$this->Recipient = str_replace('<!--$To-->', $ARecipient, $this->Recipient);
	}

	// Method to add Cc email
	function AddCc($ACc) {
		if ($ACc <> "") {
			if ($this->Cc <> "") $this->Cc .= ";";
			$this->Cc .= $ACc;
		}
	}

	// Method to add Bcc email
	function AddBcc($ABcc) {
		if ($ABcc <> "")  {
			if ($this->Bcc <> "") $this->Bcc .= ";";
			$this->Bcc .= $ABcc;
		}
	}

	// Method to replace subject
	function ReplaceSubject($ASubject) {
		$this->Subject = str_replace('<!--$Subject-->', $ASubject, $this->Subject);
	}

	// Method to replace content
	function ReplaceContent($Find, $ReplaceWith) {
		$this->Content = str_replace($Find, $ReplaceWith, $this->Content);
	}

	// Method to send email
	function Send() {
		return ew_SendEmail($this->Sender, $this->Recipient, $this->Cc, $this->Bcc,
			$this->Subject, $this->Content, $this->Format);
	}
}

/**
 * Pager item class
 */

class cPagerItem {
	var $Start;
	var $Text;
	var $Enabled;
}

/**
 * Numeric pager class
 */

class cNumericPager {
	var $Items = array();
	var $Count, $FromIndex, $ToIndex, $RecordCount, $PageSize, $Range;
	var $FirstButton, $PrevButton, $NextButton, $LastButton;
	var $ButtonCount = 0;

	function cNumericPager($StartRec, $DisplayRecs, $TotalRecs, $RecRange)
	{
		$this->FirstButton = new cPagerItem;
		$this->PrevButton = new cPagerItem;
		$this->NextButton = new cPagerItem;
		$this->LastButton = new cPagerItem;
    $this->FromIndex = intval($StartRec);
		$this->PageSize = intval($DisplayRecs);
		$this->RecordCount = intval($TotalRecs);
		$this->Range = intval($RecRange);
		if ($this->PageSize == 0) return;
		if ($this->FromIndex > $this->RecordCount)
			$this->FromIndex = $this->RecordCount;
		$this->ToIndex = $this->FromIndex + $this->PageSize - 1;
		if ($this->ToIndex > $this->RecordCount)
			$this->ToIndex = $this->RecordCount;

		// setup
		$this->SetupNumericPager();

		// update button count
		if ($this->FirstButton->Enabled) $this->ButtonCount++;
		if ($this->PrevButton->Enabled) $this->ButtonCount++;
		if ($this->NextButton->Enabled) $this->ButtonCount++;
		if ($this->LastButton->Enabled) $this->ButtonCount++;
		$this->ButtonCount += count($this->Items);
  }

	// Add pager item
	function AddPagerItem($StartIndex, $Text, $Enabled)
	{
		$Item = new cPagerItem;
		$Item->Start = $StartIndex;
		$Item->Text = $Text;
		$Item->Enabled = $Enabled;
		$this->Items[] = $Item;
	}

	// Setup pager items
	function SetupNumericPager()
	{
		if ($this->RecordCount > $this->PageSize) {
			$Eof = ($this->RecordCount < ($this->FromIndex + $this->PageSize));
			$HasPrev = ($this->FromIndex > 1);

			// First Button
			$TempIndex = 1;
			$this->FirstButton->Start = $TempIndex;
			$this->FirstButton->Enabled = ($this->FromIndex > $TempIndex);

			// Prev Button
			$TempIndex = $this->FromIndex - $this->PageSize;
			if ($TempIndex < 1) $TempIndex = 1;
			$this->PrevButton->Start = $TempIndex;
			$this->PrevButton->Enabled = $HasPrev;

			// Page links
			if ($HasPrev || !$Eof) {
				$x = 1;
				$y = 1;
				$dx1 = intval(($this->FromIndex-1)/($this->PageSize*$this->Range))*$this->PageSize*$this->Range + 1;
				$dy1 = intval(($this->FromIndex-1)/($this->PageSize*$this->Range))*$this->Range + 1;
				if (($dx1+$this->PageSize*$this->Range-1) > $this->RecordCount) {
					$dx2 = intval($this->RecordCount/$this->PageSize)*$this->PageSize + 1;
					$dy2 = intval($this->RecordCount/$this->PageSize) + 1;
				} else {
					$dx2 = $dx1 + $this->PageSize*$this->Range - 1;
					$dy2 = $dy1 + $this->Range - 1;
				}
				while ($x <= $this->RecordCount) {
					if ($x >= $dx1 && $x <= $dx2) {
						$this->AddPagerItem($x, $y, $this->FromIndex<>$x);
						$x += $this->PageSize;
						$y++;
					} elseif ($x >= ($dx1-$this->PageSize*$this->Range) && $x <= ($dx2+$this->PageSize*$this->Range)) {
						if ($x+$this->Range*$this->PageSize < $this->RecordCount) {
							$this->AddPagerItem($x, $y . "-" . ($y+$this->Range-1), TRUE);
						} else {
							$ny = intval(($this->RecordCount-1)/$this->PageSize) + 1;
							if ($ny == $y) {
								$this->AddPagerItem($x, $y, TRUE);
							} else {
								$this->AddPagerItem($x, $y . "-" . $ny, TRUE);
							}
						}
						$x += $this->Range*$this->PageSize;
						$y += $this->Range;
					} else {
						$x += $this->Range*$this->PageSize;
						$y += $this->Range;
					}
				}
			}

			// Next Button
			$TempIndex = $this->FromIndex + $this->PageSize;
			$this->NextButton->Start = $TempIndex;
			$this->NextButton->Enabled = !$Eof;

			// Last Button
			$TempIndex = intval(($this->RecordCount-1)/$this->PageSize)*$this->PageSize + 1;
			$this->LastButton->Start = $TempIndex;
			$this->LastButton->Enabled = ($this->FromIndex < $TempIndex);
		}
	}
}

/**
 * PrevNext pager class
 */

class cPrevNextPager {
	var $FirstButton, $PrevButton, $NextButton, $LastButton;
	var $CurrentPage, $PageCount, $FromIndex, $ToIndex, $RecordCount;

	function cPrevNextPager($StartRec, $DisplayRecs, $TotalRecs)
	{
		$this->FirstButton = new cPagerItem;
		$this->PrevButton = new cPagerItem;
		$this->NextButton = new cPagerItem;
		$this->LastButton = new cPagerItem;
		$this->FromIndex = intval($StartRec);
		$this->PageSize = intval($DisplayRecs);
		$this->RecordCount = intval($TotalRecs);
		if ($this->PageSize == 0) return;
		$this->CurrentPage = intval(($this->FromIndex-1)/$this->PageSize) + 1;
		$this->PageCount = intval(($this->RecordCount-1)/$this->PageSize) + 1;
		if ($this->FromIndex > $this->RecordCount)
			$this->FromIndex = $this->RecordCount;
		$this->ToIndex = $this->FromIndex + $this->PageSize - 1;
		if ($this->ToIndex > $this->RecordCount)
			$this->ToIndex = $this->RecordCount;

		// First Button
		$TempIndex = 1;
		$this->FirstButton->Start = $TempIndex;
		$this->FirstButton->Enabled = ($TempIndex <> $this->FromIndex);

		// Prev Button
		$TempIndex = $this->FromIndex - $this->PageSize;
		if ($TempIndex < 1) $TempIndex = 1;
		$this->PrevButton->Start = $TempIndex;
		$this->PrevButton->Enabled = ($TempIndex <> $this->FromIndex);

		// Next Button
		$TempIndex = $this->FromIndex + $this->PageSize;
		if ($TempIndex > $this->RecordCount)
			$TempIndex = $this->FromIndex;
		$this->NextButton->Start = $TempIndex;
		$this->NextButton->Enabled = ($TempIndex <> $this->FromIndex);

		// Last Button
		$TempIndex = intval(($this->RecordCount-1)/$this->PageSize)*$this->PageSize + 1;
		$this->LastButton->Start = $TempIndex;
		$this->LastButton->Enabled = ($TempIndex <> $this->FromIndex);
  }
}

/**
 * Field class
 */

class cField {
	var $TblVar; // Table var
	var $FldName; // Field name
	var $FldVar; // Field var
	var $FldExpression; // Field expression (used in sql)
	var $FldType; // Field type
	var $FldDataType; // PHPMaker Field type
	var $AdvancedSearch; // AdvancedSearch Object
	var $Upload; // Upload Object
	var $FldDateTimeFormat; // Date time format
	var $CssStyle; // Css style
	var $CssClass; // Css class
	var $ImageAlt; // Image alt
	var $ImageWidth = 0; // Image width
	var $ImageHeight = 0; // Image height
	var $ViewCustomAttributes; // View custom attributes
	var $EditCustomAttributes; // Edit custom attributes
	var $Count; // Count
	var $Total; // Total
	var $TrueValue = '1';
	var $FalseValue = '0';

	function cField($tblvar, $fldvar, $fldname, $fldexpression, $fldtype, $flddtfmt, $upload = FALSE) {
		$this->TblVar = $tblvar;
		$this->FldVar = $fldvar;
		$this->FldName = $fldname;
		$this->FldExpression = $fldexpression;
		$this->FldType = $fldtype;
		$this->FldDataType = ew_FieldDataType($fldtype);
		$this->FldDateTimeFormat = $flddtfmt;
		$this->AdvancedSearch = new cAdvancedSearch();
		if ($upload) $this->Upload = new cUpload($this->TblVar, $this->FldVar, ($this->FldDataType == EW_DATATYPE_BLOB));
	}

	// View Attributes
	function ViewAttributes() {
		$sAtt = "";
		if (trim($this->CssStyle) <> "") {
			$sAtt .= " style=\"" . trim($this->CssStyle) . "\"";
		}
		if (trim($this->CssClass) <> "") {
			$sAtt .= " class=\"" . trim($this->CssClass) . "\"";
		}
		if (trim($this->ImageAlt) <> "") {
			$sAtt .= " alt=\"" . trim($this->ImageAlt) . "\"";
		}
		if (intval($this->ImageWidth) > 0) {
			$sAtt .= " width=\"" . intval($this->ImageWidth) . "\"";
		}
		if (intval($this->ImageHeight) > 0) {
			$sAtt .= " height=\"" . intval($this->ImageHeight) . "\"";
		}
		if (trim($this->ViewCustomAttributes) <> "") {
			$sAtt .= " " . trim($this->ViewCustomAttributes);
		}
		return $sAtt;
	}

	// Edit Attributes
	function EditAttributes() {
		$sAtt = "";
		if (trim($this->CssStyle) <> "") {
			$sAtt .= " style=\"" . trim($this->CssStyle) . "\"";
		}
		if (trim($this->CssClass) <> "") {
			$sAtt .= " class=\"" . trim($this->CssClass) . "\"";
		}
		if (trim($this->EditCustomAttributes) <> "") {
			$sAtt .= " " . trim($this->EditCustomAttributes);
		}
		return $sAtt;
	}
	var $CellCssClass; // Cell Css class
	var $CellCssStyle; // Cell Css style

	// Cell Attributes
	function CellAttributes() {
		$sAtt = "";
		if (trim($this->CellCssStyle) <> "") {
			$sAtt .= " style=\"" . trim($this->CellCssStyle) . "\"";
		}
		if (trim($this->CellCssClass) <> "") {
			$sAtt .= " class=\"" . trim($this->CellCssClass) . "\"";
		}
		return $sAtt;
	}

	// Sort Attributes
	function getSort() {
		return @$_SESSION[EW_PROJECT_NAME . "_" . $this->TblVar . "_" . EW_TABLE_SORT . "_" . $this->FldVar];
	}

	function setSort($v) {
		if (@$_SESSION[EW_PROJECT_NAME . "_" . $this->TblVar . "_" . EW_TABLE_SORT . "_" . $this->FldVar] <> $v) {
			$_SESSION[EW_PROJECT_NAME . "_" . $this->TblVar . "_" . EW_TABLE_SORT . "_" . $this->FldVar] = $v;
		}
	}

	function ReverseSort() {
		return ($this->getSort() == "ASC") ? "DESC" : "ASC";
	}
	var $MultiUpdate; // Multi update
	var $CurrentValue; // Current value
	var $ViewValue; // View value
	var $EditValue; // Edit value
	var $EditValue2; // Edit value 2 (search)
	var $HrefValue; // Href value

	// Form value
	var $FormValue;

	function setFormValue($v) {
		$this->FormValue = ew_StripSlashes($v);
		if (is_array($this->FormValue)) $this->FormValue = implode(",", $this->FormValue);
		$this->CurrentValue = $this->FormValue;
	}

	// QueryString value
	var $QueryStringValue;

	function setQueryStringValue($v) {
		$this->QueryStringValue = ew_StripSlashes($v);
		$this->CurrentValue = $this->QueryStringValue;
	}

	// Database Value
	var $DbValue;

	function setDbValue($v) {
		$this->DbValue = $v;
		$this->CurrentValue = $this->DbValue;
	}

	// Set database value with error default
	function SetDbValueDef($value, $default) {
		switch ($this->FldType) {
			case 2:
			case 3:
			case 16:
			case 17:
			case 18:  // Int
				$DbValue = (is_numeric($value)) ? intval($value) : $default;
				break;
			case 19:
			case 20:
			case 21: // Big Int
				$DbValue = (is_numeric($value)) ? $value : $default;
				break;
			case 5:
			case 6:
			case 14:
			case 131: // Double
			case 4: // Single
				if (function_exists('floatval')) { // PHP 4 >= 4.2.0
					$DbValue = (is_numeric($value)) ? floatval($value) : $default;
				} else {
					$DbValue = (is_numeric($value)) ? (float)$value : $default;
				}
				break;
			case 7:
			case 133:
			case 134:
			case 135: //Date
			case 201:
			case 203:
			case 129:
			case 130:
			case 200:
			case 202: // String
				$DbValue = trim($value);
				if ($DbValue == "") $DbValue = $default;
				break;
			case 128:
			case 204:
			case 205: // Binary
				$DbValue = is_null($value) ? $default : $value;
				break;
			case 72: // GUID
				if (function_exists('preg_match')) {
					$p1 = '/^{{1}([0-9a-fA-F]){8}-([0-9a-fA-F]){4}-([0-9a-fA-F]){4}-([0-9a-fA-F]){4}-([0-9a-fA-F]){12}}{1}$/';
					$p2 = '/^([0-9a-fA-F]){8}-([0-9a-fA-F]){4}-([0-9a-fA-F]){4}-([0-9a-fA-F]){4}-([0-9a-fA-F]){12}$/';
					$DbValue = (preg_match($p1, trim($value)) || preg_match($p2, trim($value))) ? trim($value) : $default;
				} else {
					$DbValue = (is_string($value) && ((strlen($value) == 38 && strspn($value, '{}-0123456789abcdefABCDEF') == 38)) ||
						(strlen($value) == 36 && strspn($value, '-0123456789abcdefABCDEF') == 36)) ? $value : $default;
				}
				break;
			default:
				$DbValue = $value;
		}
		$this->setDbValue($DbValue);
	}

	// Session Value
	function getSessionValue() {
		return @$_SESSION[EW_PROJECT_NAME . "_" . $this->TblVar . "_" . $this->FldVar . "_SessionValue"];
	}

	function setSessionValue($v) {
		$_SESSION[EW_PROJECT_NAME . "_" . $this->TblVar . "_" . $this->FldVar . "_SessionValue"] = $v;
	}
}
?>
<?php

/**
 * Advanced Search class
 */

class cAdvancedSearch {
	var $SearchValue; // Search value
	var $SearchOperator; // Search operator
	var $SearchCondition; // Search condition
	var $SearchValue2; // Search value 2
	var $SearchOperator2; // Search operator 2
}
?>
<?php

/**
 * Upload class
 */

class cUpload {
	var $Index = 0; // Index to handle multiple form elements
	var $TblVar; // Table variable
	var $FldVar; // Field variable
	var $Message; // Error message
	var $DbValue; // Value from database
	var $Value = NULL; // Upload value
	var $Binary = NULL; // Temporary file
	var $IsBinary; // Is BLOB field
	var $Action; // Upload action
	var $UploadPath; // Upload path
	var $FileName; // Upload file name
	var $FileSize; // Upload file size
	var $ContentType; // File content type
	var $ImageWidth; // Image width
	var $ImageHeight; // Image height	

	// Class initialize
	function cUpload($TblVar, $FldVar, $Binary = FALSE) {
		$this->TblVar = $TblVar;
		$this->FldVar = $FldVar;
		$this->IsBinary = $Binary;
	}

	function getSessionID() {
		return EW_PROJECT_NAME . "_" . $this->TblVar . "_" . $this->FldVar . "_" . $this->Index;
	}

	// Save Db value to Session
	function SaveDbToSession() {
		$sSessionID = $this->getSessionID();
		$_SESSION[$sSessionID . "_DbValue"] = $this->DbValue;
	}

	// Restore Db value from Session
	function RestoreDbFromSession() {
		$sSessionID = $this->getSessionID();
		$this->DbValue = @$_SESSION[$sSessionID . "_DbValue"];
	}

	// Remove Db value from Session
	function RemoveDbFromSession() {
		$sSessionID = $this->getSessionID();
		unset($_SESSION[$sSessionID . "_DbValue"]);
	}

	// Save Upload values to Session
	function SaveToSession() {
		$sSessionID = $this->getSessionID();
		$_SESSION[$sSessionID . "_Action"] = $this->Action;
		$_SESSION[$sSessionID . "_FileSize"] = $this->FileSize;
		$_SESSION[$sSessionID . "_FileName"] = $this->FileName;
		$_SESSION[$sSessionID . "_ContentType"] = $this->ContentType;
		$_SESSION[$sSessionID . "_ImageWidth"] = $this->ImageWidth;
		$_SESSION[$sSessionID . "_ImageHeight"] = $this->ImageHeight;
		$path = pathinfo($this->FileName);
		$ext = @$path['extension'];
		if ($ext == '') $ext = 'tmp';
		$f = tempnam(ew_TmpFolder(), 'tmp') . '.' . $ext;
		if (!is_null($this->Value)) {
			if (rename($this->Value, $this->Value . '.' . $ext)) {
		 		$this->Value .= '.' . $ext;
			} elseif (move_uploaded_file($this->Value, $f)) {
				$this->Value = $f;
			}
		}
		$_SESSION[$sSessionID . "_Value"] = $this->Value;
	}

	// Restore Upload values from Session
	function RestoreFromSession() {
		$sSessionID = $this->getSessionID();
		$this->Action = @$_SESSION[$sSessionID . "_Action"];
		$this->FileSize = @$_SESSION[$sSessionID . "_FileSize"];
		$this->FileName = @$_SESSION[$sSessionID . "_FileName"];
		$this->ContentType = @$_SESSION[$sSessionID . "_ContentType"];
		$this->ImageWidth = @$_SESSION[$sSessionID . "_ImageWidth"];
		$this->ImageHeight = @$_SESSION[$sSessionID . "_ImageHeight"];
		$this->Value = @$_SESSION[$sSessionID . "_Value"];
	}

	// Remove Upload values from Session
	function RemoveFromSession() {
		$sSessionID = $this->getSessionID();
		unset($_SESSION[$sSessionID . "_Action"]);
		unset($_SESSION[$sSessionID . "_FileSize"]);
		unset($_SESSION[$sSessionID . "_FileName"]);
		unset($_SESSION[$sSessionID . "_ContentType"]);
		unset($_SESSION[$sSessionID . "_ImageWidth"]);
		unset($_SESSION[$sSessionID . "_ImageHeight"]);
		if (is_file($this->Value)) @unlink($this->Value);
		unset($_SESSION[$sSessionID . "_Value"]);
	}

	// function to check the file type of the uploaded file
	function UploadAllowedFileExt($filename) {
		if (trim($filename) == "") return TRUE;
		$extension = substr(strtolower(strrchr($filename, ".")), 1);
		$allowExt = explode(",", strtolower(EW_UPLOAD_ALLOWED_FILE_EXT));
		return in_array($extension, $allowExt);
	}

	// Get upload file
	function UploadFile() {
		global $objForm;
		$this->Value = NULL; // Reset first
		$sFldVar = $this->FldVar;
		$sFldVarAction = "a" . substr($sFldVar, 1);
		$sFldVarWidth = "wd" . substr($sFldVar, 1);
		$sFldVarHeight = "ht" . substr($sFldVar, 1);

		// Get action
		$this->Action = $objForm->GetValue($sFldVarAction);

		// Get and check the upload file size
		$this->FileSize = $objForm->GetUploadFileSize($sFldVar);
		if ($this->FileSize > 0 && intval(EW_MAX_FILE_SIZE) > 0) {
			if ($this->FileSize > intval(EW_MAX_FILE_SIZE)) {
				$this->Message = str_replace("%s", EW_MAX_FILE_SIZE, "Max. file size (%s bytes) exceeded.");
				return FALSE;
			}
		}

		// Get and check the upload file type
		$this->FileName = $objForm->GetUploadFileName($sFldVar);
		$this->FileName = str_replace(" ", "_", $this->FileName); // Replace space with underscore
		if (!$this->UploadAllowedFileExt($this->FileName)) {
			$this->Message = "File type is not allowed.";
			return FALSE;
		}

		// Get upload file content type
		$this->ContentType = $objForm->GetUploadFileContentType($sFldVar);

		// Get upload value
		//$this->Value = $objForm->GetUploadFileData($sFldVar);

		if ($objForm->IsUploadedFile($sFldVar)) {
			$this->Value = $objForm->GetUploadFileTmpName($sFldVar); // store the tmp file name only
		}

		// Get image width and height
		$this->ImageWidth = $objForm->GetUploadImageWidth($sFldVar);
		$this->ImageHeight = $objForm->GetUploadImageHeight($sFldVar);
		if ($this->ImageWidth < 0 || $this->ImageHeight < 0) {
			$this->ImageWidth = $objForm->GetValue($sFldVarWidth);
			$this->ImageHeight = $objForm->GetValue($sFldVarHeight);
		}
		return TRUE; // Normal return
	}

	// Resize image
	function Resize($width, $height, $quality) {
		if (!is_null($this->Value)) {
			$wrkwidth = $width;
			$wrkheight = $height;
			if ($this->IsBinary) {
				$this->Binary = ew_ResizeFileToBinary($this->Value, $wrkwidth, $wrkheight, $quality);
				$this->FileSize = strlen($this->Binary);
			} else {
				ew_ResizeFile($this->Value, $this->Value, $wrkwidth, $wrkheight, $quality);
				$this->FileSize = filesize($this->Value);
			}
			$this->ImageWidth = $wrkwidth;
			$this->ImageHeight = $wrkheight;
		}
	}

	// Get binary date
	function GetBinary() {
		if (is_null($this->Binary)) {
			if (!is_null($this->Value)) return ew_ReadFile($this->Value);
		} else {
			return $this->Binary;
		}
		return NULL;
	}
}
?>
<?php

/**
 * Advanced Security class
 */

class cAdvancedSecurity {
	var $UserLevel = array();
	var $UserLevelPriv = array();

	// Current user name
	function getCurrentUserName() {
		return strval(@$_SESSION[EW_SESSION_USER_NAME]);
	}

	function setCurrentUserName($v) {
		$_SESSION[EW_SESSION_USER_NAME] = $v;
	}

	function CurrentUserName() {
		return $this->getCurrentUserName();
	}

	// Current User ID
	function getCurrentUserID() {
		return strval(@$_SESSION[EW_SESSION_USER_ID]);
	}

	function setCurrentUserID($v) {
		$_SESSION[EW_SESSION_USER_ID] = $v;
	}

	function CurrentUserID() {
		return $this->getCurrentUserID();
	}

	// Current parent User ID
	function getCurrentParentUserID() {
		return strval(@$_SESSION[EW_SESSION_PARENT_USER_ID]);
	}

	function setCurrentParentUserID($v) {
		$_SESSION[EW_SESSION_PARENT_USER_ID] = $v;
	}

	function CurrentParentUserID() {
		return $this->getCurrentParentUserID();
	}

	// Current User Level id
	function getCurrentUserLevelID() {
		return @$_SESSION[EW_SESSION_USER_LEVEL_ID];
	}

	function setCurrentUserLevelID($v) {
		$_SESSION[EW_SESSION_USER_LEVEL_ID] = $v;
	}

	function CurrentUserLevelID() {
		return $this->getCurrentUserLevelID();
	}

	// Current User Level value
	function getCurrentUserLevel() {
		return @$_SESSION[EW_SESSION_USER_LEVEL];
	}

	function setCurrentUserLevel($v) {
		$_SESSION[EW_SESSION_USER_LEVEL] = $v;
	}

	function CurrentUserLevel() {
		return $this->getCurrentUserLevel();
	}

	// Can add
	function CanAdd() {
		return (($this->CurrentUserLevel() & EW_ALLOW_ADD) == EW_ALLOW_ADD);
	}

	// Can delete
	function CanDelete() {
		return (($this->CurrentUserLevel() & EW_ALLOW_DELETE) == EW_ALLOW_DELETE);
	}

	// Can edit
	function CanEdit() {
		return (($this->CurrentUserLevel() & EW_ALLOW_EDIT) == EW_ALLOW_EDIT);
	}

	// Can view
	function CanView() {
		return (($this->CurrentUserLevel() & EW_ALLOW_VIEW) == EW_ALLOW_VIEW);
	}

	// Can list
	function CanList() {
		return (($this->CurrentUserLevel() & EW_ALLOW_LIST) == EW_ALLOW_LIST);
	}

	// Can report
	function CanReport() {
		return (($this->CurrentUserLevel() & EW_ALLOW_REPORT) == EW_ALLOW_REPORT);
	}

	// Can search
	function CanSearch() {
		return (($this->CurrentUserLevel() & EW_ALLOW_SEARCH) == EW_ALLOW_SEARCH);
	}

	// Can admin
	function CanAdmin() {
		return (($this->CurrentUserLevel() & EW_ALLOW_ADMIN) == EW_ALLOW_ADMIN);
	}

	// Last url
	function LastUrl() {

		//globlal $_COOKIE;
		return @$_COOKIE[EW_PROJECT_NAME]['LastUrl'];
	}

	// Save last url
	function SaveLastUrl() {
		$s = ew_ServerVar("SCRIPT_NAME");
		$q = ew_ServerVar("QUERY_STRING");
		if ($q <> "") $s .= "?" . $q;
		if ($this->LastUrl() == $s) $s = "";
		@setcookie(EW_PROJECT_NAME . '[LastUrl]', $s);
	}

	// Auto login
	function AutoLogin() {
		if (@$_COOKIE[EW_PROJECT_NAME]['AutoLogin'] == "autologin") {
			$usr = @$_COOKIE[EW_PROJECT_NAME]['UserName'];
			$pwd = @$_COOKIE[EW_PROJECT_NAME]['Password'];
			$pwd = TEAdecrypt($pwd, EW_RANDOM_KEY);
			$AutoLogin = $this->ValidateUser($usr, $pwd);
		} else {
			$AutoLogin = FALSE;
		}
		return $AutoLogin;
	}

	// Validate user
	function ValidateUser($usr, $pwd) {
		global $conn;
		global $admin;
		$ValidateUser = FALSE;

		// Check hard coded admin first
		if (EW_CASE_SENSITIVE_PASSWORD) {
			$ValidateUser = (EW_ADMIN_USER_NAME == $usr && EW_ADMIN_PASSWORD == $pwd);
		} else {
			$ValidateUser = (strtolower(EW_ADMIN_USER_NAME) == strtolower($usr) &&
				strtolower(EW_ADMIN_PASSWORD) == strtolower($pwd));
		}
		if ($ValidateUser) {
			$_SESSION[EW_SESSION_STATUS] = "login";
			$_SESSION[EW_SESSION_SYS_ADMIN] = 1; // System Administrator
			$this->setCurrentUserName("Administrator"); // Load user name
			$this->setCurrentUserLevelID(-1); // System Administrator
			$this->SetUpUserLevel();
		}

		// Check other users
		if (!$ValidateUser) {
			$sFilter = "(`username` = '" . ew_AdjustSql($usr) . "')";

			// Set up filter (Sql Where Clause) and get Return Sql
			// Sql constructor in <UseTable> class, <UserTable>info.php

			$admin->CurrentFilter = $sFilter;
			$sSql = $admin->SQL();
			if ($rs = $conn->Execute($sSql)) {
				if (!$rs->EOF) {
					if (EW_CASE_SENSITIVE_PASSWORD) {
						if (EW_MD5_PASSWORD) {
							$ValidateUser = ($rs->fields('password') == md5($pwd));
						} else {
							$ValidateUser = ($rs->fields('password') == $pwd);
						}
					} else {
						if (EW_MD5_PASSWORD) {
							$ValidateUser = ($rs->fields('password') == md5(strtolower($pwd)));
						} else {
							$ValidateUser = (strtolower($rs->fields('password')) == strtolower($pwd));
						}
					}
					if ($ValidateUser) {
						$_SESSION[EW_SESSION_STATUS] = "login";
						$_SESSION[EW_SESSION_SYS_ADMIN] = 0; // Non System Administrator
						$this->setCurrentUserName($rs->fields('username')); // Load user name
						if (is_null($rs->fields('privilegi_admin'))) {
							$this->setCurrentUserLevelID(0);
						} else {
							$this->setCurrentUserLevelID(intval($rs->fields('privilegi_admin'))); // Load User Level
						}
						$this->SetUpUserLevel();
					}
				}
				$rs->Close();
			}
		}
		return $ValidateUser;
	}

	// Static User Level security
	function SetUpUserLevel() {

		// User Level definitions
		array_splice($this->UserLevel, 0);
		$this->UserLevel[] = array("0", "Default");
		$this->UserLevel[] = array("1", "Manager");
		array_splice($this->UserLevelPriv, 0);
		$this->UserLevelPriv[] = array("multiprocesssteps", 0, 0);
		$this->UserLevelPriv[] = array("multiprocesssteps", 1, 0);
		$this->UserLevelPriv[] = array("process", 0, 0);
		$this->UserLevelPriv[] = array("process", 1, 8);
		$this->UserLevelPriv[] = array("processparams", 0, 0);
		$this->UserLevelPriv[] = array("processparams", 1, 0);
		$this->UserLevelPriv[] = array("processparamsvalue", 0, 0);
		$this->UserLevelPriv[] = array("processparamsvalue", 1, 0);
		$this->UserLevelPriv[] = array("setprocess", 0, 0);
		$this->UserLevelPriv[] = array("setprocess", 1, 8);
		$this->UserLevelPriv[] = array("processstatus", 0, 0);
		$this->UserLevelPriv[] = array("processstatus", 1, 8);
		$this->UserLevelPriv[] = array("admin", 0, 0);
		$this->UserLevelPriv[] = array("admin", 1, 0);
		$this->UserLevelPriv[] = array("paramsconnection", 0, 0);
		$this->UserLevelPriv[] = array("paramsconnection", 1, 8);

		// Save the User Level to session variable
		$this->SaveUserLevel();
	}

	// Load current User Level
	function LoadCurrentUserLevel($Table) {
		$this->LoadUserLevel();
		$this->setCurrentUserLevel($this->CurrentUserLevelPriv($Table));
	}

	// Get current user privilege
	function CurrentUserLevelPriv($TableName) {
		if ($this->IsLoggedIn()) {
			return $this->GetUserLevelPrivEx($TableName, $this->CurrentUserLevelID());
		} else {

			//return $this->GetUserLevelPrivEx($TableName, 0);
			return 0;
		}
	}

	// Get user privilege based on table name and User Level
	function GetUserLevelPrivEx($TableName, $UserLevelID) {
		if (strval($UserLevelID) == "-1") { // System Administrator
			if (defined("EW_USER_LEVEL_COMPAT")) {
				return 31; // Use old User Level values
			} else {
				return 127; // Use new User Level values (separate View/Search)
			}
		} elseif ($UserLevelID >= 0) {
			if (is_array($this->UserLevelPriv)) {
				foreach ($this->UserLevelPriv as $row) {
					list($table, $levelid, $priv) = $row;
					if (strtolower($table) == strtolower($TableName) && strval($levelid) == strval($UserLevelID)) {
						if (is_null($priv) || !is_numeric($priv)) return 0;
						return intval($priv);
					}
				}
			}
		}
		return 0;
	}

	// Get current User Level name
	function CurrentUserLevelName() {
		return $this->GetUserLevelName($this->CurrentUserLevelID());
	}

	// Get User Level name based on User Level
	function GetUserLevelName($UserLevelID) {
		if (strval($UserLevelID) == "-1") {
			return "Administrator";
		} elseif ($UserLevelID >= 0) {
			if (is_array($this->UserLevel)) {
				foreach ($this->UserLevel as $row) {
					list($levelid, $name) = $row;
					if (strval($levelid) == strval($UserLevelID))	return $name;
				}
			}
		}
		return "";
	}

	// function to display all the User Level settings (for debug only)
	function ShowUserLevelInfo() {
		echo "<pre class=\"phpmaker\">";
		print_r($this->UserLevel);
		print_r($this->UserLevelPriv);
		echo "</pre>";
		echo "<p>CurrentUserLevel = " . $this->CurrentUserLevel() . "</p>";
	}

	// function to check privilege for List page (for menu items)
	function AllowList($TableName) {
		return ($this->CurrentUserLevelPriv($TableName) & EW_ALLOW_LIST);
	}

	// Check if user is logged in
	function IsLoggedIn() {
		return (@$_SESSION[EW_SESSION_STATUS] == "login");
	}

	// Check if user is system administrator
	function IsSysAdmin() {
		return (@$_SESSION[EW_SESSION_SYS_ADMIN] == 1);
	}

	// Check if user is administrator
	function IsAdmin() {
		return ($this->CurrentUserLevelID() == -1 || $this->IsSysAdmin());
	}

	// Save User Level to session
	function SaveUserLevel() {
		$_SESSION[EW_SESSION_AR_USER_LEVEL] = $this->UserLevel;
		$_SESSION[EW_SESSION_AR_USER_LEVEL_PRIV] = $this->UserLevelPriv;
	}

	// Load User Level from session
	function LoadUserLevel() {
		if (!is_array(@$_SESSION[EW_SESSION_AR_USER_LEVEL])) {
			$this->SetupUserLevel();
			$this->SaveUserLevel();
		} else {
			$this->UserLevel = $_SESSION[EW_SESSION_AR_USER_LEVEL];
			$this->UserLevelPriv = $_SESSION[EW_SESSION_AR_USER_LEVEL_PRIV];
		}
	}

	// function to get user info
	function CurrentUserInfo($fieldname) {
		$conn;
		$info = NULL;
		if ($this->CurrentUserName() == "") return $info;

		// Set up filter (Sql Where Clause) and get Return Sql
		// Sql constructor in <UseTable> class, <UserTable>info.php

		$sFilter = "(`username` = '" . ew_AdjustSql($this->CurrentUserName()) . "')";
		$admin->CurrentFilter = $sFilter;
		$sSql = $admin->SQL();
		if ($rs = $conn->Execute($sSql)) {
			if (!$rs->EOF) $info = $rs->fields($fieldname);
			$rs->Close();
		}
		return $info;
	}
}
?>
<?php

/**
 * Common functions
 */

// Connection/Query error handler
function ew_ErrorFn($DbType, $ErrorType, $ErrorNo, $ErrorMsg, $Param1, $Param2, $Object) {
	if ($ErrorType == 'CONNECT') {
		$msg = "Failed to connect to $Param2 at $Param1. Error: " . $ErrorMsg;
	} elseif ($ErrorType == 'EXECUTE') {
		$msg = "Failed to execute SQL: $Param1. Error: " . $ErrorMsg;
	} 
	$_SESSION[EW_SESSION_MESSAGE] = $msg;
}

// Connect to database
function &ew_Connect() {
	$object = new mysqlt_driver_ADOConnection();
	if (defined("EW_DEBUG_ENABLED")) $object->debug = TRUE;
	$object->port = EW_CONN_PORT;
	$object->raiseErrorFn = 'ew_ErrorFn';
	$object->Connect(EW_CONN_HOST, EW_CONN_USER, EW_CONN_PASS, EW_CONN_DB);
	if (EW_MYSQL_CHARSET <> "") $object->Execute("SET NAMES '" . EW_MYSQL_CHARSET . "'");
	$object->raiseErrorFn = '';
	return $object;
}

// Get server variable by name
function ew_ServerVar($Name) {
	$str = @$_SERVER[$Name];
	if (empty($str)) $str = @$_ENV[$Name];
	return $str;
}

// Check if HTTP POST
function ew_IsHttpPost() {
	$ct = ew_ServerVar("CONTENT_TYPE");
	if (empty($ct)) $ct = ew_ServerVar("HTTP_CONTENT_TYPE");
	return ($ct == "application/x-www-form-urlencoded");
}

// Get script name
function ew_ScriptName() {
	$sn = ew_ServerVar("PHP_SELF");
	if (empty($sn)) $sn = ew_ServerVar("SCRIPT_NAME");
	if (empty($sn)) $sn = ew_ServerVar("ORIG_PATH_INFO");
	if (empty($sn)) $sn = ew_ServerVar("ORIG_SCRIPT_NAME");
	if (empty($sn)) $sn = ew_ServerVar("REQUEST_URI");
	if (empty($sn)) $sn = ew_ServerVar("URL");
	if (empty($sn)) $sn = "UNKNOWN";
	return $sn;
}

// Check if valid operator
function ew_IsValidOpr($Opr, $FldType) {
	$Valid = ($Opr == "=" || $Opr == "<" || $Opr == "<=" ||
		$Opr == ">" || $Opr == ">=" || $Opr == "<>");
	if ($FldType == EW_DATATYPE_STRING || $FldType == EW_DATATYPE_MEMO) {
		$Valid = ($Valid || $Opr == "LIKE" || $Opr == "NOT LIKE" ||
			$Opr == "STARTS WITH");
	}
	return $Valid; 
}

// quote field values
function ew_QuotedValue($Value, $FldType) {
	if (is_null($Value)) return "NULL";
	switch ($FldType) {
	case EW_DATATYPE_STRING:
	case EW_DATATYPE_BLOB:
	case EW_DATATYPE_MEMO:
	case EW_DATATYPE_TIME:
		return "'" . ew_AdjustSql($Value) . "'";
	case EW_DATATYPE_DATE:
		return (EW_IS_MSACCESS) ? "#" . ew_AdjustSql($Value) . "#" :
			"'" . ew_AdjustSql($Value) . "'";
	case EW_DATATYPE_GUID:
		if (EW_IS_MSACCESS) {
			if (strlen($Value) == 38) {
				return "{guid " . $Value . "}";
			} elseif (strlen($Value) == 36) {
				return "{guid {" . $Value . "}}";
			}
		} else {
		  return "'" . $Value . "'";
		}
	case EW_DATATYPE_BOOLEAN: // enum('Y'/'N') or enum('1'/'0')
		return "'" . $Value . "'";
	default:
		return $Value;
	}
}

// Convert different data type value
function ew_Conv($v, $t) {
	switch ($t) {
	case 2:
	case 3:
	case 16:
	case 17:
	case 18:
	case 19: // adSmallInt/adInteger/adTinyInt/adUnsignedTinyInt/adUnsignedSmallInt
		return (is_null($v)) ? NULL : intval($v);
	case 4:
	Case 5:
	case 6:
	case 131: // adSingle/adDouble/adCurrency/adNumeric
		if (function_exists('floatval')) { // PHP 4 >= 4.2.0
			return (is_null($v)) ? NULL : floatval($v);
		} else {
			return (is_null($v)) ? NULL : (float)$v;
		}
	default:
		return (is_null($v)) ? NULL : $v;
	}
}

// function for debug
function ew_Trace($msg) {
	$filename = "debug.txt";
	if (!$handle = fopen($filename, 'a')) exit;
	if (is_writable($filename)) fwrite($handle, $msg . "\n");
	fclose($handle);
}

// function to compare values with special handling for null values
function ew_CompareValue($v1, $v2) {
	if (is_null($v1) && is_null($v2)) {
		return TRUE;
	} elseif (is_null($v1) || is_null($v2)) {
		return FALSE;
	} else {
		return ($v1 == $v2);
	}
}

// Strip slashes
function ew_StripSlashes($value) {
	if (!get_magic_quotes_gpc()) return $value;
	if (is_array($value)) { 
		return array_map('ew_StripSlashes', $value);
	} else {
		return stripslashes($value);
	}
}

// Add slashes for SQL
function ew_AdjustSql($val) {
	$val = addslashes(trim($val));
	return $val;
}

// Build sql based on different sql part
function ew_BuildSql($sSelect, $sWhere, $sGroupBy, $sHaving, $sOrderBy, $sFilter, $sSort) {
	$sDbWhere = $sWhere;
	if ($sDbWhere <> "") $sDbWhere = "(" . $sDbWhere . ")";
	if ($sFilter <> "") {
		if ($sDbWhere <> "") $sDbWhere .= " AND ";
		$sDbWhere .= "(" . $sFilter . ")";
	}
	$sDbOrderBy = $sOrderBy;
	if ($sSort <> "") $sDbOrderBy = $sSort;
	$sSql = $sSelect;
	if ($sDbWhere <> "") $sSql .= " WHERE " . $sDbWhere;
	if ($sGroupBy <> "") $sSql .= " GROUP BY " . $sGroupBy;
	if ($sHaving <> "") $sql .= " HAVING " . $sHaving;
	if ($sDbOrderBy <> "") $sSql .= " ORDER BY " . $sDbOrderBy;
	return $sSql;
}

// Executes the query, and returns the first column of the first row
function ew_ExecuteScalar($SQL) {
	global $conn;
	if ($conn) {
		if ($rs = $conn->Execute($SQL)) {
			if (!$rs->EOF && $rs->FieldCount() > 0)
				return $rs->fields[0];
		}
	}
	return NULL;
}

// Write Audit Trail (login/logout)
function ew_WriteAuditTrailOnLogInOut($logtype) {
	$table = $logtype;
	$sKey = "";

	// Write Audit Trail
	$filePfx = "log";
	$curDate = date("Y/m/d");
	$curTime = date("H:i:s");
	$id = ew_ScriptName();
	$user = CurrentUserName();
	$action = $logtype;
	ew_WriteAuditTrail($filePfx, $curDate, $curTime, $id, $user, $action, $table, "", "", "", "");
}

// Function for writing audit trail
function ew_WriteAuditTrail($pfx, $curDate, $curTime, $id, $user, $action, $table, $field, $keyvalue, $oldvalue, $newvalue) {
	global $conn;
	$sFolder = "";
	$sFolder = str_replace("/", EW_PATH_DELIMITER, $sFolder);
	$ewFilePath = ew_AppRoot() . $sFolder;
	$sTab = "\t";
	$userwrk = $user;
	if ($userwrk == "") $userwrk = "-1"; // assume Administrator if no user
	$sHeader = "date" . $sTab . "time" . $sTab . "id" . 
				$sTab .	"user" . $sTab . "action" . $sTab . "table" . 
				$sTab . "field" . $sTab . "key value" . $sTab . "old value" . 
				$sTab . "new value";
	$sMsg = $curDate . $sTab . $curTime . $sTab . 
			$id . $sTab . $user . $sTab . 
			$action . $sTab . $table . $sTab . 
			$field . $sTab . $keyvalue . $sTab . 
			$oldvalue . $sTab . $newvalue;
	$sFolder = EW_AUDIT_TRAIL_PATH;
	$sFn = $pfx . "_" . date("Ymd") . ".txt";
	$filename = ew_UploadPathEx(TRUE, $sFolder) . $sFn;
	if (file_exists($filename)) {
		$fileHandler = fopen($filename, "a+b");
	} else {
		$fileHandler = fopen($filename, "a+b");
		fwrite($fileHandler,$sHeader."\r\n");
	}
	fwrite($fileHandler, $sMsg."\r\n");
	fclose($fileHandler);

	// Sample code to write audit trail to database
	// (change the table and names according to your table schema)

	$sAuditSql = "INSERT INTO AuditTrailTable (`date`, `time`, `id`, `user`, " .
		"`action`, `table`, `field`, `keyvalue`, `oldvalue`, `newvalue`) VALUES (" .
		"'" . ew_AdjustSql($curDate) . "', " .
		"'" . ew_AdjustSql($curTime) . "', " .
		"'" . ew_AdjustSql($id) . "', " .
		"'" . ew_AdjustSql($user) . "', " .
		"'" . ew_AdjustSql($action) . "', " .
		"'" . ew_AdjustSql($table) . "', " .
		"'" . ew_AdjustSql($field) . "', " .
		"'" . ew_AdjustSql($keyvalue) . "', " .
		"'" . ew_AdjustSql($oldvalue) . "', " .
		"'" . ew_AdjustSql($newvalue) . "')";

		// echo sAuditSql; // uncomment to debug
	$conn->Execute($sAuditSql);
}

// Unformat date time based on format type
function ew_UnFormatDateTime($dt, $namedformat) {
	$dt = trim($dt);
	while (strpos($dt, "  ") !== FALSE) $dt = str_replace("  ", " ", $dt);
	$arDateTime = explode(" ", $dt);
	if (count($arDateTime) == 0) return $dt;
	$arDatePt = explode(EW_DATE_SEPARATOR, $arDateTime[0]);
	if ($namedformat == 0) {
		$arDefFmt = explode(EW_DATE_SEPARATOR, EW_DEFAULT_DATE_FORMAT);
		if ($arDefFmt[0] == "yyyy") {
			$namedformat = 9;
		} elseif ($arDefFmt[0] == "mm") {
			$namedformat = 10;
		} elseif ($arDefFmt[0] == "dd") {
			$namedformat = 11;
		}
	}
	if (count($arDatePt) == 3) {
		switch ($namedformat) {
		case 5:
		case 9: //yyyymmdd
			list($year, $month, $day) = $arDatePt;
			break;
		case 6:
		case 10: //mmddyyyy
			list($month, $day, $year) = $arDatePt;
			break;
		case 7:
		case 11: //ddmmyyyy
			list($day, $month, $year) = $arDatePt;
			break;
		default:
			return $dt;
		}
		if (strlen($year) <= 4 && strlen($month) <= 2 && strlen($day) <= 2) {
			return $year . "-" . str_pad($month, 2, "0", STR_PAD_LEFT) . "-" .
				 str_pad($day, 2, "0", STR_PAD_LEFT) .
				((count($arDateTime) > 1) ? " " . $arDateTime[1] : "");
		} else {
			return $dt;
		}
	} else {
		return $dt;
	}
}

//-------------------------------------------------------------------------------
// Functions for default date format
// FormatDateTime
//Format a timestamp, datetime, date or time field from MySQL
//$namedformat:
//0 - General Date,
//1 - Long Date,
//2 - Short Date (Default),
//3 - Long Time,
//4 - Short Time (hh:mm:ss),
//5 - Short Date (yyyy/mm/dd),
//6 - Short Date (mm/dd/yyyy),
//7 - Short Date (dd/mm/yyyy),
//8 - Short Date (Default) + Short Time (if not 00:00:00)
//9 - Short Date (yyyy/mm/dd) + Short Time (hh:mm:ss),
//10 - Short Date (mm/dd/yyyy) + Short Time (hh:mm:ss),
//11 - Short Date (dd/mm/yyyy) + Short Time (hh:mm:ss)
function ew_FormatDateTime($ts, $namedformat) {
	$DefDateFormat = str_replace("yyyy", "%Y", EW_DEFAULT_DATE_FORMAT);
	$DefDateFormat = str_replace("mm", "%m", $DefDateFormat);
	$DefDateFormat = str_replace("dd", "%d", $DefDateFormat);
	if (is_numeric($ts)) // timestamp
	{
		switch (strlen($ts)) {
			case 14:
				$patt = '/(\d{4})(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})/';
				break;
			case 12:
				$patt = '/(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})/';
				break;
			case 10:
				$patt = '/(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})/';
				break;
			case 8:
				$patt = '/(\d{4})(\d{2})(\d{2})/';
				break;
			case 6:
				$patt = '/(\d{2})(\d{2})(\d{2})/';
				break;
			case 4:
				$patt = '/(\d{2})(\d{2})/';
				break;
			case 2:
				$patt = '/(\d{2})/';
				break;
			default:
				return $ts;
		}
		if ((isset($patt))&&(preg_match($patt, $ts, $matches)))
		{
			$year = $matches[1];
			$month = @$matches[2];
			$day = @$matches[3];
			$hour = @$matches[4];
			$min = @$matches[5];
			$sec = @$matches[6];
		}
		if (($namedformat==0)&&(strlen($ts)<10)) $namedformat = 2;
	}
	elseif (is_string($ts))
	{
		if (preg_match('/(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})/', $ts, $matches)) // datetime
		{
			$year = $matches[1];
			$month = $matches[2];
			$day = $matches[3];
			$hour = $matches[4];
			$min = $matches[5];
			$sec = $matches[6];
		}
		elseif (preg_match('/(\d{4})-(\d{2})-(\d{2})/', $ts, $matches)) // date
		{
			$year = $matches[1];
			$month = $matches[2];
			$day = $matches[3];
			if ($namedformat==0) $namedformat = 2;
		}
		elseif (preg_match('/(^|\s)(\d{2}):(\d{2}):(\d{2})/', $ts, $matches)) // time
		{
			$hour = $matches[2];
			$min = $matches[3];
			$sec = $matches[4];
			if (($namedformat==0)||($namedformat==1)) $namedformat = 3;
			if ($namedformat==2) $namedformat = 4;
		}
		else
		{
			return $ts;
		}
	}
	else
	{
		return $ts;
	}
	if (!isset($year)) $year = 0; // dummy value for times
	if (!isset($month)) $month = 1;
	if (!isset($day)) $day = 1;
	if (!isset($hour)) $hour = 0;
	if (!isset($min)) $min = 0;
	if (!isset($sec)) $sec = 0;
	$uts = @mktime($hour, $min, $sec, $month, $day, $year);
	if ($uts < 0 || $uts == FALSE) { // failed to convert
		$year = substr_replace("0000", $year, -1 * strlen($year));
		$month = substr_replace("00", $month, -1 * strlen($month));
		$day = substr_replace("00", $day, -1 * strlen($day));
		$hour = substr_replace("00", $hour, -1 * strlen($hour));
		$min = substr_replace("00", $min, -1 * strlen($min));
		$sec = substr_replace("00", $sec, -1 * strlen($sec));
		$DefDateFormat = str_replace("yyyy", $year, EW_DEFAULT_DATE_FORMAT);
		$DefDateFormat = str_replace("mm", $month, $DefDateFormat);
		$DefDateFormat = str_replace("dd", $day, $DefDateFormat);
		switch ($namedformat) {
			case 0:
				return $DefDateFormat." $hour:$min:$sec";
				break;
			case 1://unsupported, return general date
				return $DefDateFormat." $hour:$min:$sec";
				break;
			case 2:
				return $DefDateFormat;
				break;
			case 3:
				if (intval($hour)==0)
					return "12:$min:$sec AM";
				elseif (intval($hour)>0 && intval($hour)<12)
					return "$hour:$min:$sec AM";
				elseif (intval($hour)==12)
					return "$hour:$min:$sec PM";
				elseif (intval($hour)>12 && intval($hour)<=23)
					return (intval($hour)-12).":$min:$sec PM";
				else
					return "$hour:$min:$sec";
				break;
			case 4:
				return "$hour:$min:$sec";
				break;
			case 5:
				return "$year". EW_DATE_SEPARATOR . "$month" . EW_DATE_SEPARATOR . "$day";
				break;
			case 6:
				return "$month". EW_DATE_SEPARATOR ."$day" . EW_DATE_SEPARATOR . "$year";
				break;
			case 7:
				return "$day" . EW_DATE_SEPARATOR ."$month" . EW_DATE_SEPARATOR . "$year";
				break;
			case 8:
				return $DefDateFormat . (($hour == 0 && $min == 0 && $sec == 0) ? "" : " $hour:$min:$sec");
				break;
			case 9:
				return "$year". EW_DATE_SEPARATOR . "$month" . EW_DATE_SEPARATOR . "$day $hour:$min:$sec";
				break;
			case 10:
				return "$month". EW_DATE_SEPARATOR ."$day" . EW_DATE_SEPARATOR . "$year $hour:$min:$sec";
				break;
			case 11:
				return "$day" . EW_DATE_SEPARATOR ."$month" . EW_DATE_SEPARATOR . "$year $hour:$min:$sec";
				break;
		}
	} else {
		switch ($namedformat) {
			case 0:
				return strftime($DefDateFormat." %H:%M:%S", $uts);
				break;
			case 1:
				return strftime("%A, %B %d, %Y", $uts);
				break;
			case 2:
				return strftime($DefDateFormat, $uts);
				break;
			case 3:
				return strftime("%I:%M:%S %p", $uts);
				break;
			case 4:
				return strftime("%H:%M:%S", $uts);
				break;
			case 5:
				return strftime("%Y" . EW_DATE_SEPARATOR . "%m" . EW_DATE_SEPARATOR . "%d", $uts);
				break;
			case 6:
				return strftime("%m" . EW_DATE_SEPARATOR . "%d" . EW_DATE_SEPARATOR . "%Y", $uts);
				break;
			case 7:
				return strftime("%d" . EW_DATE_SEPARATOR . "%m" . EW_DATE_SEPARATOR . "%Y", $uts);
				break;
			case 8:
				return strftime($DefDateFormat . (($hour == 0 && $min == 0 && $sec == 0) ? "" : " %H:%M:%S"), $uts);
				break;
			case 9:
				return strftime("%Y" . EW_DATE_SEPARATOR . "%m" . EW_DATE_SEPARATOR . "%d %H:%M:%S", $uts);
				break;
			case 10:
				return strftime("%m" . EW_DATE_SEPARATOR . "%d" . EW_DATE_SEPARATOR . "%Y %H:%M:%S", $uts);
				break;
			case 11:
				return strftime("%d" . EW_DATE_SEPARATOR . "%m" . EW_DATE_SEPARATOR . "%Y %H:%M:%S", $uts);
				break;
		}
	}
}

// FormatCurrency
//ew_FormatCurrency(Expression[,NumDigitsAfterDecimal [,IncludeLeadingDigit
// [,UseParensForNegativeNumbers [,GroupDigits]]]])
//NumDigitsAfterDecimal is the numeric value indicating how many places to the
//right of the decimal are displayed
//-1 Use Default
//The IncludeLeadingDigit, UseParensForNegativeNumbers, and GroupDigits
//arguments have the following settings:
//-1 True
//0 False
//-2 Use Default
function ew_FormatCurrency($amount, $NumDigitsAfterDecimal, $IncludeLeadingDigit = -2, $UseParensForNegativeNumbers = -2, $GroupDigits = -2) {

	// export the values returned by localeconv into the local scope
	//if (function_exists("localeconv"))

		extract(localeconv()); // PHP 4 >= 4.0.5

	// set defaults if locale is not set
	if (empty($currency_symbol)) $currency_symbol = DEFAULT_CURRENCY_SYMBOL;
	if (empty($mon_decimal_point)) $mon_decimal_point = DEFAULT_MON_DECIMAL_POINT;
	if (empty($mon_thousands_sep)) $mon_thousands_sep = DEFAULT_MON_THOUSANDS_SEP;
	if (empty($positive_sign)) $positive_sign = DEFAULT_POSITIVE_SIGN;
	if (empty($negative_sign)) $negative_sign = DEFAULT_NEGATIVE_SIGN;
	if (empty($frac_digits) || $frac_digits == CHAR_MAX) $frac_digits = DEFAULT_FRAC_DIGITS;
	if (empty($p_cs_precedes) || $p_cs_precedes == CHAR_MAX) $p_cs_precedes = DEFAULT_P_CS_PRECEDES;
	if (empty($p_sep_by_space) || $p_sep_by_space == CHAR_MAX) $p_sep_by_space = DEFAULT_P_SEP_BY_SPACE;
	if (empty($n_cs_precedes) || $n_cs_precedes == CHAR_MAX) $n_cs_precedes = DEFAULT_N_CS_PRECEDES;
	if (empty($n_sep_by_space) || $n_sep_by_space == CHAR_MAX) $n_sep_by_space = DEFAULT_N_SEP_BY_SPACE;
	if (empty($p_sign_posn) || $p_sign_posn == CHAR_MAX) $p_sign_posn = DEFAULT_P_SIGN_POSN;
	if (empty($n_sign_posn) || $n_sign_posn == CHAR_MAX) $n_sign_posn = DEFAULT_N_SIGN_POSN;

	// check $NumDigitsAfterDecimal
	if ($NumDigitsAfterDecimal > -1)
		$frac_digits = $NumDigitsAfterDecimal;

	// check $UseParensForNegativeNumbers
	if ($UseParensForNegativeNumbers == -1) {
		$n_sign_posn = 0;
		if ($p_sign_posn == 0) {
			if (DEFAULT_P_SIGN_POSN != 0)
				$p_sign_posn = DEFAULT_P_SIGN_POSN;
			else
				$p_sign_posn = 3;
		}
	} elseif ($UseParensForNegativeNumbers == 0) {
		if ($n_sign_posn == 0)
			if (DEFAULT_P_SIGN_POSN != 0)
				$n_sign_posn = DEFAULT_P_SIGN_POSN;
			else
				$n_sign_posn = 3;
	}

	// check $GroupDigits
	if ($GroupDigits == -1) {
		$mon_thousands_sep = DEFAULT_MON_THOUSANDS_SEP;
	} elseif ($GroupDigits == 0) {
		$mon_thousands_sep = "";
	}

	// start by formatting the unsigned number
	$number = number_format(abs($amount),
							$frac_digits,
							$mon_decimal_point,
							$mon_thousands_sep);

	// check $IncludeLeadingDigit
	if ($IncludeLeadingDigit == 0) {
		if (substr($number, 0, 2) == "0.")
			$number = substr($number, 1, strlen($number)-1);
	}
	if ($amount < 0) {
		$sign = $negative_sign;

		// "extracts" the boolean value as an integer
		$n_cs_precedes  = intval($n_cs_precedes  == true);
		$n_sep_by_space = intval($n_sep_by_space == true);
		$key = $n_cs_precedes . $n_sep_by_space . $n_sign_posn;
	} else {
		$sign = $positive_sign;
		$p_cs_precedes  = intval($p_cs_precedes  == true);
		$p_sep_by_space = intval($p_sep_by_space == true);
		$key = $p_cs_precedes . $p_sep_by_space . $p_sign_posn;
	}
	$formats = array(

	  // currency symbol is after amount
	  // no space between amount and sign

	  '000' => '(%s' . $currency_symbol . ')',
	  '001' => $sign . '%s ' . $currency_symbol,
	  '002' => '%s' . $currency_symbol . $sign,
	  '003' => '%s' . $sign . $currency_symbol,
	  '004' => '%s' . $sign . $currency_symbol,

	  // one space between amount and sign
	  '010' => '(%s ' . $currency_symbol . ')',
	  '011' => $sign . '%s ' . $currency_symbol,
	  '012' => '%s ' . $currency_symbol . $sign,
	  '013' => '%s ' . $sign . $currency_symbol,
	  '014' => '%s ' . $sign . $currency_symbol,

	  // currency symbol is before amount
	  // no space between amount and sign

	  '100' => '(' . $currency_symbol . '%s)',
	  '101' => $sign . $currency_symbol . '%s',
	  '102' => $currency_symbol . '%s' . $sign,
	  '103' => $sign . $currency_symbol . '%s',
	  '104' => $currency_symbol . $sign . '%s',

	  // one space between amount and sign
	  '110' => '(' . $currency_symbol . ' %s)',
	  '111' => $sign . $currency_symbol . ' %s',
	  '112' => $currency_symbol . ' %s' . $sign,
	  '113' => $sign . $currency_symbol . ' %s',
	  '114' => $currency_symbol . ' ' . $sign . '%s');

  // lookup the key in the above array
	return sprintf($formats[$key], $number);
}

// FormatNumber
//ew_FormatNumber(Expression[,NumDigitsAfterDecimal [,IncludeLeadingDigit
//	[,UseParensForNegativeNumbers [,GroupDigits]]]])
//NumDigitsAfterDecimal is the numeric value indicating how many places to the
//right of the decimal are displayed
//-1 Use Default
//The IncludeLeadingDigit, UseParensForNegativeNumbers, and GroupDigits
//arguments have the following settings:
//-1 True
//0 False
//-2 Use Default
function ew_FormatNumber($amount, $NumDigitsAfterDecimal, $IncludeLeadingDigit = -2, $UseParensForNegativeNumbers = -2, $GroupDigits = -2) {

	// export the values returned by localeconv into the local scope
	//if (function_exists("localeconv"))

		extract(localeconv()); // PHP 4 >= 4.0.5

	// set defaults if locale is not set
	if (empty($currency_symbol)) $currency_symbol = DEFAULT_CURRENCY_SYMBOL;
	if (empty($mon_decimal_point)) $mon_decimal_point = DEFAULT_MON_DECIMAL_POINT;
	if (empty($mon_thousands_sep)) $mon_thousands_sep = DEFAULT_MON_THOUSANDS_SEP;
	if (empty($positive_sign)) $positive_sign = DEFAULT_POSITIVE_SIGN;
	if (empty($negative_sign)) $negative_sign = DEFAULT_NEGATIVE_SIGN;
	if (empty($frac_digits) || $frac_digits == CHAR_MAX) $frac_digits = DEFAULT_FRAC_DIGITS;
	if (empty($p_cs_precedes) || $p_cs_precedes == CHAR_MAX) $p_cs_precedes = DEFAULT_P_CS_PRECEDES;
	if (empty($p_sep_by_space) || $p_sep_by_space == CHAR_MAX) $p_sep_by_space = DEFAULT_P_SEP_BY_SPACE;
	if (empty($n_cs_precedes) || $n_cs_precedes == CHAR_MAX) $n_cs_precedes = DEFAULT_N_CS_PRECEDES;
	if (empty($n_sep_by_space) || $n_sep_by_space == CHAR_MAX) $n_sep_by_space = DEFAULT_N_SEP_BY_SPACE;
	if (empty($p_sign_posn) || $p_sign_posn == CHAR_MAX) $p_sign_posn = DEFAULT_P_SIGN_POSN;
	if (empty($n_sign_posn) || $n_sign_posn == CHAR_MAX) $n_sign_posn = DEFAULT_N_SIGN_POSN;

	// check $NumDigitsAfterDecimal
	if ($NumDigitsAfterDecimal > -1)
		$frac_digits = $NumDigitsAfterDecimal;

	// check $UseParensForNegativeNumbers
	if ($UseParensForNegativeNumbers == -1) {
		$n_sign_posn = 0;
		if ($p_sign_posn == 0) {
			if (DEFAULT_P_SIGN_POSN != 0)
				$p_sign_posn = DEFAULT_P_SIGN_POSN;
			else
				$p_sign_posn = 3;
		}
	} elseif ($UseParensForNegativeNumbers == 0) {
		if ($n_sign_posn == 0)
			if (DEFAULT_P_SIGN_POSN != 0)
				$n_sign_posn = DEFAULT_P_SIGN_POSN;
			else
				$n_sign_posn = 3;
	}

	// check $GroupDigits
	if ($GroupDigits == -1) {
		$mon_thousands_sep = DEFAULT_MON_THOUSANDS_SEP;
	} elseif ($GroupDigits == 0) {
		$mon_thousands_sep = "";
	}

	// start by formatting the unsigned number
	$number = number_format(abs($amount),
						  $frac_digits,
						  $mon_decimal_point,
						  $mon_thousands_sep);

	// check $IncludeLeadingDigit
	if ($IncludeLeadingDigit == 0) {
		if (substr($number, 0, 2) == "0.")
			$number = substr($number, 1, strlen($number)-1);
	}
	if ($amount < 0) {
		$sign = $negative_sign;
		$key = $n_sign_posn;
	} else {
		$sign = $positive_sign;
		$key = $p_sign_posn;
	}
	$formats = array(
		'0' => '(%s)',
		'1' => $sign . '%s',
		'2' => $sign . '%s',
		'3' => $sign . '%s',
		'4' => $sign . '%s');

	// lookup the key in the above array
	return sprintf($formats[$key], $number);
}

// FormatPercent
//ew_FormatPercent(Expression[,NumDigitsAfterDecimal [,IncludeLeadingDigit
//	[,UseParensForNegativeNumbers [,GroupDigits]]]])
//NumDigitsAfterDecimal is the numeric value indicating how many places to the
//right of the decimal are displayed
//-1 Use Default
//The IncludeLeadingDigit, UseParensForNegativeNumbers, and GroupDigits
//arguments have the following settings:
//-1 True
//0 False
//-2 Use Default
function ew_FormatPercent($amount, $NumDigitsAfterDecimal, $IncludeLeadingDigit = -2, $UseParensForNegativeNumbers = -2, $GroupDigits = -2) {

	// export the values returned by localeconv into the local scope
	//if (function_exists("localeconv"))

		extract(localeconv()); // PHP 4 >= 4.0.5

	// set defaults if locale is not set
	if (empty($currency_symbol)) $currency_symbol = DEFAULT_CURRENCY_SYMBOL;
	if (empty($mon_decimal_point)) $mon_decimal_point = DEFAULT_MON_DECIMAL_POINT;
	if (empty($mon_thousands_sep)) $mon_thousands_sep = DEFAULT_MON_THOUSANDS_SEP;
	if (empty($positive_sign)) $positive_sign = DEFAULT_POSITIVE_SIGN;
	if (empty($negative_sign)) $negative_sign = DEFAULT_NEGATIVE_SIGN;
	if (empty($frac_digits) || $frac_digits == CHAR_MAX) $frac_digits = DEFAULT_FRAC_DIGITS;
	if (empty($p_cs_precedes) || $p_cs_precedes == CHAR_MAX) $p_cs_precedes = DEFAULT_P_CS_PRECEDES;
	if (empty($p_sep_by_space) || $p_sep_by_space == CHAR_MAX) $p_sep_by_space = DEFAULT_P_SEP_BY_SPACE;
	if (empty($n_cs_precedes) || $n_cs_precedes == CHAR_MAX) $n_cs_precedes = DEFAULT_N_CS_PRECEDES;
	if (empty($n_sep_by_space) || $n_sep_by_space == CHAR_MAX) $n_sep_by_space = DEFAULT_N_SEP_BY_SPACE;
	if (empty($p_sign_posn) || $p_sign_posn == CHAR_MAX) $p_sign_posn = DEFAULT_P_SIGN_POSN;
	if (empty($n_sign_posn) || $n_sign_posn == CHAR_MAX) $n_sign_posn = DEFAULT_N_SIGN_POSN;

	// check $NumDigitsAfterDecimal
	if ($NumDigitsAfterDecimal > -1)
		$frac_digits = $NumDigitsAfterDecimal;

	// check $UseParensForNegativeNumbers
	if ($UseParensForNegativeNumbers == -1) {
		$n_sign_posn = 0;
		if ($p_sign_posn == 0) {
			if (DEFAULT_P_SIGN_POSN != 0)
				$p_sign_posn = DEFAULT_P_SIGN_POSN;
			else
				$p_sign_posn = 3;
		}
	} elseif ($UseParensForNegativeNumbers == 0) {
		if ($n_sign_posn == 0)
			if (DEFAULT_P_SIGN_POSN != 0)
				$n_sign_posn = DEFAULT_P_SIGN_POSN;
			else
				$n_sign_posn = 3;
	}

	// check $GroupDigits
	if ($GroupDigits == -1) {
		$mon_thousands_sep = DEFAULT_MON_THOUSANDS_SEP;
	} elseif ($GroupDigits == 0) {
		$mon_thousands_sep = "";
	}

	// start by formatting the unsigned number
	$number = number_format(abs($amount)*100,
							$frac_digits,
							$mon_decimal_point,
							$mon_thousands_sep);

	// check $IncludeLeadingDigit
	if ($IncludeLeadingDigit == 0) {
		if (substr($number, 0, 2) == "0.")
			$number = substr($number, 1, strlen($number)-1);
	}
	if ($amount < 0) {
		$sign = $negative_sign;
		$key = $n_sign_posn;
	} else {
		$sign = $positive_sign;
		$key = $p_sign_posn;
	}
	$formats = array(
		'0' => '(%s%%)',
		'1' => $sign . '%s%%',
		'2' => $sign . '%s%%',
		'3' => $sign . '%s%%',
		'4' => $sign . '%s%%');

	// lookup the key in the above array
	return sprintf($formats[$key], $number);
}

// Encode html
function ew_HtmlEncode($exp) {
	return htmlspecialchars(strval($exp));
}

// Generate Value Separator based on current row count
// rowcnt - zero based row count
function ew_ValueSeparator($rowcnt) {
	return ", ";
}

// Generate View Option Separator based on current row count (Multi-Select / CheckBox)
// rowcnt - zero based row count
function ew_ViewOptionSeparator($rowcnt) {
	$sep = ", ";

	// Sample code to adjust 2 options per row
	//if (($rowcnt + 1) % 2 == 0) { // 2 options per row
		//return $sep += "<br>";
	//}

	return $sep;
}

// Move uploaded file
function ew_MoveUploadFile($srcfile, $destfile) {
	$res = move_uploaded_file($srcfile, $destfile);
	if ($res) chmod($destfile, EW_UPLOADED_FILE_MODE);
	return $res;
}

// Render repeat column table
// rowcnt - zero based row count
function ew_RepeatColumnTable($totcnt, $rowcnt, $repeatcnt, $rendertype) {
	$sWrk = "";
	if ($rendertype == 1) { // Render control start
		if ($rowcnt == 0) $sWrk .= "<table class=\"phpmakerlist\">";
		if ($rowcnt % $repeatcnt == 0) $sWrk .= "<tr>";
		$sWrk .= "<td>";
	} elseif ($rendertype == 2) { // Render control end
		$sWrk .= "</td>";
		if ($rowcnt % $repeatcnt == $repeatcnt - 1) {
			$sWrk .= "</tr>";
		} elseif ($rowcnt == $totcnt - 1) {
			for ($i = ($rowcnt % $repeatcnt) + 1; $i < $repeatcnt; $i++) {
				$sWrk .= "<td>&nbsp;</td>";
			}
			$sWrk .= "</tr>";
		}
		if ($rowcnt == $totcnt - 1) $sWrk .= "</table>";
	}
	return $sWrk;
}

// Truncate Memo Field based on specified length, string truncated to nearest space or CrLf
function ew_TruncateMemo($str, $ln) {
	if (strlen($str) > 0 && strlen($str) > $ln) {
		$k = 0;
		while ($k >= 0 && $k < strlen($str)) {
			$i = strpos($str, " ", $k);
			$j = strpos($str, chr(10), $k);
			if ($i === FALSE && $j === FALSE) { // Not able to truncate
				return $str;
			} else {

				// Get nearest space or CrLf
				if ($i > 0 && $j > 0) {
					if ($i < $j) {
						$k = $i;
					} else {
						$k = $j;
					}
				} elseif ($i > 0) {
					$k = $i;
				} elseif ($j > 0) {
					$k = $j;
				}

				// Get truncated text
				if ($k >= $ln) {
					return substr($str, 0, $k) . "...";
				} else {
					$k++;
				}
			}
		}
	} else {
		return $str;
	}
}

// Send notify email
function ew_SendNotifyEmail($sFn, $sSubject, $sTable, $sKey, $sAction) {

	// Send Email
	if (EW_SENDER_EMAIL <> "" && EW_RECIPIENT_EMAIL <> "") {
		$Email = new cEmail;
		$Email->Load($sFn);
		$Email->ReplaceSender(EW_SENDER_EMAIL); // Replace Sender
		$Email->ReplaceRecipient(EW_RECIPIENT_EMAIL); // Replace Recipient
		$Email->ReplaceSubject($sSubject); // Replace Subject
		$Email->ReplaceContent("<!--table-->", $sTable);
		$Email->ReplaceContent("<!--key-->", $sKey);
		$Email->ReplaceContent("<!--action-->", $sAction);
		$Email->Send();
	}
}

// Include PHPMailer class is selected
if (EW_EMAIL_COMPONENT == "PHPMAILER") {
	include("phpmailer" . EW_PATH_DELIMITER . "class.phpmailer.php");
}

// Function to send email
function ew_SendEmail($sFrEmail, $sToEmail, $sCcEmail, $sBccEmail, $sSubject, $sMail, $sFormat) {

	/* for debug only
	echo "sSubject: " . $sSubject . "<br>";
	echo "sFrEmail: " . $sFrEmail . "<br>";
	echo "sToEmail: " . $sToEmail . "<br>";
	echo "sCcEmail: " . $sCcEmail . "<br>"; 
	echo "sSubject: " . $sSubject . "<br>";
	echo "sMail: " . $sMail . "<br>";
	echo "sFormat: " . $sFormat . "<br>";
	*/
	if (EW_EMAIL_COMPONENT == "PHPMAILER") {
		$mail = new PHPMailer();
		$mail->IsSMTP(); 
		$mail->Host = EW_SMTP_SERVER;
		$mail->SMTPAuth = (EW_SMTP_SERVER_USERNAME <> "" && EW_SMTP_SERVER_PASSWORD <> "");
		$mail->Username = EW_SMTP_SERVER_USERNAME;
		$mail->Password = EW_SMTP_SERVER_PASSWORD;
		$mail->Port = EW_SMTP_SERVER_PORT;
		$mail->From = $sFrEmail;
		$mail->FromName = $sFrEmail;
		$mail->Subject = $sSubject;
		$mail->Body = $sMail;
		$sToEmail = str_replace(";", ",", $sToEmail);
		$arrTo = explode(",", $sToEmail);
		foreach ($arrTo as $sTo) {
			$mail->AddAddress(trim($sTo));
		}
		if ($sCcEmail <> "") {
			$sCcEmail = str_replace(";", ",", $sCcEmail);
			$arrCc = explode(",", $sCcEmail);
			foreach ($arrCc as $sCc) {
				$mail->AddCC(trim($sCc));
			}
		}
		if ($sBccEmail <> "") {
			$sBccEmail = str_replace(";", ",", $sBccEmail);
			$arrBcc = explode(",", $sBccEmail);
			foreach ($arrBcc as $sBcc) {
				$mail->AddBCC(trim($sBcc));
			}
		}
		if (strtolower($sFormat) == "html") {
			$mail->ContentType = "text/html";
		} else {
			$mail->ContentType = "text/plain";
		}
		$res = $mail->Send();
		$mail->ClearAddresses();
		$mail->ClearAttachments();
		return $res;
	} else {
		$to  = $sToEmail;
		$subject = $sSubject;
		$headers = "";
		if (strtolower($sFormat) == "html") {
			$content_type = "text/html";
		} else {
			$content_type = "text/plain";
		}
		$headers = "Content-type: " . $content_type . "\r\n";
		$message = $sMail;
		$headers .= "From: " . str_replace(";", ",", $sFrEmail) . "\r\n";
		if ($sCcEmail <> "") {
			$headers .= "Cc: " . str_replace(";", ",", $sCcEmail) . "\r\n";
		}
		if ($sBccEmail <>"") {
			$headers .= "Bcc: " . str_replace(";", ",", $sBccEmail) . "\r\n";
		}
		if (EW_IS_WINDOWS) {
			ini_set("SMTP", EW_SMTP_SERVER);
			ini_set("smtp_port", EW_SMTP_SERVER_PORT);
		}
		ini_set("sendmail_from", $sFrEmail);
		return mail($to, $subject, $message, $headers);
	}
}

// Field data type
function ew_FieldDataType($fldtype) {
	switch ($fldtype) {
		case 20:
		case 3:
		case 2:
		case 16:
		case 4:
		case 5:
		case 131:
		case 6:
		case 17:
		case 18:
		case 19:
		case 21: // Numeric
			return EW_DATATYPE_NUMBER;
		case 7:
		case 133:
		case 135: // Date
			return EW_DATATYPE_DATE;
		case 134: // Time
			return EW_DATATYPE_TIME;
		case 201:
		case 203: // Memo
			return EW_DATATYPE_MEMO;
		case 129:
		case 130:
		case 200:
		case 202: // String
			return EW_DATATYPE_STRING;
		case 11: // Boolean
			return EW_DATATYPE_BOOLEAN;
		case 72: // GUID
			return 5;
		case 128:
		case 204:
		case 205: // Binary
			return EW_DATATYPE_BLOB;
		default:
			return EW_DATATYPE_OTHER;
	}
}

// function to get application root
function ew_AppRoot() {

	// 1. use root relative path
	if (EW_ROOT_RELATIVE_PATH <> "") {
		$Path = realpath(EW_ROOT_RELATIVE_PATH);
		$Path = str_replace("\\\\", EW_PATH_DELIMITER, $Path);
	}

	// 2. if empty, use the document root if available
	if (empty($Path)) $Path = ew_ServerVar("DOCUMENT_ROOT");

	// 3. if empty, use current folder
	if (empty($Path)) $Path = realpath(".");

	// 4. use custom path, uncomment the following line and enter your path
	// e.g. $Path = 'C:\Inetpub\wwwroot\MyWebRoot'; // Windows
	//$Path = 'enter your path here';

	if (empty($Path)) die("Path of website root unknown.");
	return ew_IncludeTrailingDelimiter($Path, TRUE);
}

// function to include the last delimiter for a path
function ew_IncludeTrailingDelimiter($Path, $PhyPath) {
	if ($PhyPath) {
		if (substr($Path, -1) <> EW_PATH_DELIMITER) $Path .= EW_PATH_DELIMITER;
	} else {
		if (substr($Path, -1) <> "/") $Path .= "/";
	}
	return $Path;
}

// function to write the paths for config/debug only
function ew_WritePaths() {
	echo 'DOCUMENT_ROOT=' . ew_ServerVar("DOCUMENT_ROOT") . "<br>";
	echo 'EW_ROOT_RELATIVE_PATH=' . EW_ROOT_RELATIVE_PATH . "<br>";
	echo 'ew_AppRoot()=' . ew_AppRoot() . "<br>";
	echo 'realpath(".")=' . realpath(".") . "<br>";
	echo '__FILE__=' . __FILE__ . "<br>";
}

// function to return path of the uploaded file
// Parameter: If PhyPath is true(1), return physical path on the server;
// If PhyPath is false(0), return relative URL
function ew_UploadPathEx($PhyPath, $DestPath) {
	if ($PhyPath) {
		$Path = ew_AppRoot();
		$Path .= str_replace("/", EW_PATH_DELIMITER, $DestPath);
	} else {
		$Path = EW_ROOT_RELATIVE_PATH;
		$Path = str_replace("\\\\", "/", $Path);
		$Path = str_replace("\\", "/", $Path);
		$Path = ew_IncludeTrailingDelimiter($Path, FALSE) . $DestPath;
	}
	return ew_IncludeTrailingDelimiter($Path, $PhyPath);
}

// Return path of the uploaded file
// returns global upload folder, for backward compatibility only
function ew_UploadPath($PhyPath) {
	return ew_UploadPathEx($PhyPath, EW_UPLOAD_DEST_PATH);
}

function ew_UploadFileNameEx($folder, $sFileName) {

	// By default, ew_UniqueFileName() is used to get an unique file name,
	// you can change the logic here

	$sOutFileName = ew_UniqueFilename($folder, $sFileName);

	// Return computed output file name
	return $sOutFileName;
}

// function to generate an unique file name (filename(n).ext)
function ew_UniqueFilename($folder, $oriFilename) {
	if ($oriFilename == "") $oriFilename = ew_DefaultFileName();
	$oriFilename = str_replace(" ", "_", $oriFilename);
	$oriFilename = strtolower(basename($oriFilename));
	$destFullPath = $folder . $oriFilename;
	$newFilename = $oriFilename;
	$i = 1;
	if (!file_exists($folder)) {
		if (!ew_CreateFolder($folder)) {
			die("Folder does not exist: " . $folder);
		}
	}
	while (file_exists($destFullPath)) {
		$file_extension  = strtolower(strrchr($oriFilename, "."));
		$file_name = basename($oriFilename, $file_extension);
		$newFilename = $file_name . "($i)" . $file_extension;
		$destFullPath = $folder . $newFilename;
		$i++;
  }
	return $newFilename;
}

// function to create a default file name(yyyymmddhhmmss.bin)
function ew_DefaultFileName() {
	return date("YmdHis") . ".bin";
}

// Get full url
function ew_FullUrl() {
	$sUrl = "http";
	if (ew_ServerVar("HTTPS") <> "" && ew_ServerVar("HTTPS") <> "off") $sUrl .= "s";
	$sUrl .= "://";
	$sUrl .= ew_ServerVar("SERVER_NAME") . ew_ScriptName();
	return $sUrl;
}

// Convert to full url
function ew_ConvertFullUrl($url) {
	if ($url == "") return "";
	$sUrl = ew_FullUrl();
	return substr($sUrl, 0, strrpos($sUrl, "/")+1) . $url;
}

// Get a temp folder for temp file
function ew_TmpFolder() {
	$tmpfolder = NULL;
  $folders = array();
  if (EW_IS_WINDOWS) {
    $folders[] = ew_ServerVar("TEMP");
    $folders[] = ew_ServerVar("TMP");
  } else {
    $folders[] = '/tmp';
  }
	if (ini_get('upload_tmp_dir')) {
    $folders[] = ini_get('upload_tmp_dir');
  }
  foreach ($folders as $folder) {
    if (!$tmpfolder && is_dir($folder)) {
      $tmpfolder = $folder;
    }
  }

	//if ($tmpfolder) $tmpfolder = ew_IncludeTrailingDelimiter($tmpfolder, TRUE);
  return $tmpfolder;
}

// Create folder
function ew_CreateFolder($dir, $mode = 0777) {
  if (is_dir($dir) || @mkdir($dir, $mode)) return TRUE;
  if (!ew_CreateFolder(dirname($dir), $mode)) return FALSE;
  return @mkdir($dir, $mode);
}

// Load file data
function ew_ReadFile($file) {
	$content = '';
	if ($handle = @fopen($file, 'r')) {
		$content = fread($handle, filesize($file));
		fclose($handle);
	}
	return $content;
}

// Save file
function ew_SaveFile($folder, $fn, $file) {
	$res = FALSE;
	if (ew_CreateFolder($folder)) {
		$res = @rename($file, $folder . $fn);
		if (!$res) $res = @copy($file, $folder . $fn); // for PHP < 4.3.3

//		if ($handle = fopen($folder . $fn, 'w')) {
//			$res = fwrite($handle, $filedata);
//    	fclose($handle);
//		}

		if ($res) chmod($folder . $fn, EW_UPLOADED_FILE_MODE);
	}
	return $res;
}

// function to generate random number
function ew_Random() {
	if (phpversion() < "4.2.0") {
	  list($usec, $sec) = explode(' ', microtime());
	  $seed = (float) $sec + ((float) $usec * 100000);
		mt_srand($seed);
	}
	return mt_rand();
}

// function to remove CR and LF
function ew_RemoveCrLf($s) {
	if (strlen($s) > 0) {
		$s = str_replace("\n", " ", $s);
		$s = str_replace("\r", " ", $s);
		$s = str_replace("\l", " ", $s);
	}
	return $s;
}
?>
<?php

/**
 * Form class
 */

class cFormObj {
	var $Index;

	// Class Inialize
	function cFormObj() {
		$this->Index = 0;
	}

	// Get form element name based on index
	function GetIndexedName($name) {
		if ($this->Index <= 0) {
			return $name;
		} else {
			return substr($name, 0, 1) . $this->Index . substr($name, 1);
		}
	}

	// Get value for form element
	function GetValue($name) {
		$wrkname = $this->GetIndexedName($name);
		return @$_POST[$wrkname];
	}

	// Get upload file size
	function GetUploadFileSize($name) {
		$wrkname = $this->GetIndexedName($name);
		return @$_FILES[$wrkname]['size'];
	}

	// Get upload file name
	function GetUploadFileName($name) {
		$wrkname = $this->GetIndexedName($name);
		return @$_FILES[$wrkname]['name'];
	}

	// Get file content type
	function GetUploadFileContentType($name) {
		$wrkname = $this->GetIndexedName($name);
		return @$_FILES[$wrkname]['type'];
	}

	// Get file error
	function GetUploadFileError($name) {
		$wrkname = $this->GetIndexedName($name);
		return @$_FILES[$wrkname]['error'];
	}

	// Get file temp name
	function GetUploadFileTmpName($name) {
		$wrkname = $this->GetIndexedName($name);
		return @$_FILES[$wrkname]['tmp_name'];
	}

	// Check if is uplaod file
	function IsUploadedFile($name) {
		$wrkname = $this->GetIndexedName($name);
		return is_uploaded_file(@$_FILES[$wrkname]["tmp_name"]);
	}

	// Get upload file data
//	function GetUploadFileData($name) {
//		if ($this->IsUploadedFile($name)) {
//			$wrkname = $this->GetIndexedName($name);
//			return ew_ReadFile($_FILES[$wrkname]["tmp_name"]);
//		} else {
//			return NULL;
//		}
//	}
	// Get image sizes

	var $size;

	function GetImageDimension($name) {
		if (!isset($this->size)) {
			$wrkname = $this->GetIndexedName($name);
			$this->size = @getimagesize($_FILES[$wrkname]['tmp_name']);
		}
	}

	// Get file image width
	function GetUploadImageWidth($name) {
		$this->GetImageDimension($name);
		return $this->size[0];
	}

	// Get file image height
	function GetUploadImageHeight($name) {
		$this->GetImageDimension($name);
		return $this->size[1];
	}
}
?>
<?php

/**
 * Functions for image resize
 */

// Resize binary to thumbnail
function ew_ResizeBinary($filedata, $width, $height, $quality) {
	return TRUE; // No resize
}

// Resize file to thumbnail file
function ew_ResizeFile($fn, $tn, $width, $height, $quality) {
	if (file_exists($fn)) { // Copy only
		return ($fn <> $tn) ? copy($fn, $tn) : TRUE;
	} else {
		return FALSE;
	}
}

// Resize file to binary
function ew_ResizeFileToBinary($fn, $width, $height, $quality) {
	return ew_ReadFile($fn); // Return original file content only
}
?>
<?php

/**
 * Fucntions for search
 */

// Highlight value based on basic search / advanced search keywords
function ew_Highlight($src, $bkw, $bkwtype, $akw) {
	$outstr = "";
	if (strlen($src) > 0 && (strlen($bkw) > 0 || strlen($akw) > 0)) {
		$kwstr = $bkw;
		if (strlen($akw) > 0) {
			if (strlen($kwstr) > 0) $kwstr .= " ";
			$kwstr .= $akw;
		}
		$kwlist = explode(" ", $kwstr);
		$x = 0;
		ew_GetKeyword($src, $kwlist, $x, $y, $kw);
		while ($y >= 0) {
			$outstr .= substr($src, $x, $y-$x) .
				"<span name=\"ewHighlightSearch\" id=\"ewHighlightSearch\" class=\"ewHighlightSearch\">" .
				substr($src, $y, strlen($kw)) . "</span>";
			$x = $y + strlen($kw);
			ew_GetKeyword($src, $kwlist, $x, $y, $kw);
		}
		$outstr .= substr($src, $x);
	} else {
		$outstr = $src;
	}
	return $outstr;
}

// Get keyword
function ew_GetKeyword(&$src, &$kwlist, &$x, &$y, &$kw) {
	$thisy = -1;
	$thiskw = "";
	foreach ($kwlist as $wrkkw) {
		$wrkkw = trim($wrkkw);
		if (EW_HIGHLIGHT_COMPARE) { // Case-insensitive
			if (function_exists('stripos')) { // PHP 5
				$wrky = stripos($src, $wrkkw, $x);
			} else {
				$wrky = strpos(strtoupper($src), strtoupper($wrkkw), $x);
			}
		} else {
			$wrky = strpos($src, $wrkkw, $x);
		}
		if ($wrky !== FALSE) {
			if ($thisy == -1) {
				$thisy = $wrky;
				$thiskw = $wrkkw;
			} elseif ($wrky < $thisy) {
				$thisy = $wrky;
				$thiskw = $wrkkw;
			}
		}
	}
	$y = $thisy;
	$kw = $thiskw;
}
?>
<?php

/**
 * Functions for Auto-Update fields
 */

// Get user IP
function ew_CurrentUserIP() {
	return ew_ServerVar("REMOTE_ADDR");
}

// Get current host name, e.g. "www.mycompany.com"
function ew_CurrentHost() {
	return ew_ServerVar("HTTP_HOST");
}

// Get current date in default date format
// $namedformat = -1|5|6|7 (see comment for ew_FormatDateTime)
function ew_CurrentDate($namedformat = -1) {
	if ($namedformat > -1) {
		if ($namedformat == 6 || $namedformat == 7) {
			return ew_FormatDateTime(date('Y-m-d'), $namedformat);
		} else {
			return ew_FormatDateTime(date('Y-m-d'), 5);
	  }
	} else {
		return date('Y-m-d');
	}
}

// Get current time in hh:mm:ss format
function ew_CurrentTime() {
	return date("H:i:s");
}

// Get current date in default date format with time in hh:mm:ss format
// $namedformat = -1|9|10|11 (see comment for ew_FormatDateTime)
function ew_CurrentDateTime($namedformat = -1) {
	if ($namedformat > -1) {
		if ($namedformat == 10 || $namedformat == 11) {
			return ew_FormatDateTime(date('Y-m-d H:i:s'), $namedformat);
		} else {
			return ew_FormatDateTime(date('Y-m-d H:i:s'), 9);
	  }
	} else {
		return date('Y-m-d H:i:s');
	}
}

/**
 * Functions for backward compatibilty
 */

// Get current user name
function CurrentUserName() {
	global $Security;
	return (isset($Security)) ? $Security->CurrentUserName() : '';
}

// Get current user ID
function CurrentUserID() {
	global $Security;
	return (isset($Security)) ? $Security->CurrentUserID() : '';
}

// Get current parent user ID
function CurrentParentUserID() {
	global $Security;
	return (isset($Security)) ? $Security->CurrentParentUserID() : '';
}

// Get current User Level
function CurrentUserLevel() {
	global $Security;
	return (isset($Security)) ? $Security->CurrentUserLevelID() : -2;
}

// Allow list
function AllowList($TableName) {
	global $Security;
	return $Security->AllowList($TableName);
}

// Is Logged In
function IsLoggedIn() {
	global $Security;
	return $Security->IsLoggedIn();
}

// Is System Admin
function IsSysAdmin() {
	global $Security;
	return $Security->IsSysAdmin();
}

/**
 * Functions for TEA encryption/decryption
 */

function long2str($v, $w) {
	$len = count($v);
	$s = array();
	for ($i = 0; $i < $len; $i++)
	{
		$s[$i] = pack("V", $v[$i]);
	}
	if ($w) {
		return substr(join('', $s), 0, $v[$len - 1]);
	}	else {
		return join('', $s);
	}
}

function str2long($s, $w) {
	$v = unpack("V*", $s. str_repeat("\0", (4 - strlen($s) % 4) & 3));
	$v = array_values($v);
	if ($w) {
		$v[count($v)] = strlen($s);
	}
	return $v;
}

// encrypt
function TEAencrypt($str, $key) {
	if ($str == "") {
		return "";
	}
	$v = str2long($str, true);
	$k = str2long($key, false);
	if (count($k) < 4) {
		for ($i = count($k); $i < 4; $i++) {
			$k[$i] = 0;
		}
	}
	$n = count($v) - 1;
	$z = $v[$n];
	$y = $v[0];
	$delta = 0x9E3779B9;
	$q = floor(6 + 52 / ($n + 1));
	$sum = 0;
	while (0 < $q--) {
		$sum = int32($sum + $delta);
		$e = $sum >> 2 & 3;
		for ($p = 0; $p < $n; $p++) {
			$y = $v[$p + 1];
			$mx = int32((($z >> 5 & 0x07ffffff) ^ $y << 2) + (($y >> 3 & 0x1fffffff) ^ $z << 4)) ^ int32(($sum ^ $y) + ($k[$p & 3 ^ $e] ^ $z));
			$z = $v[$p] = int32($v[$p] + $mx);
		}
		$y = $v[0];
		$mx = int32((($z >> 5 & 0x07ffffff) ^ $y << 2) + (($y >> 3 & 0x1fffffff) ^ $z << 4)) ^ int32(($sum ^ $y) + ($k[$p & 3 ^ $e] ^ $z));
		$z = $v[$n] = int32($v[$n] + $mx);
	}
	return ew_UrlEncode(long2str($v, false));
}

// decrypt
function TEAdecrypt($str, $key) {
	$str = ew_UrlDecode($str);
	if ($str == "") {
		return "";
	}
	$v = str2long($str, false);
	$k = str2long($key, false);
	if (count($k) < 4) {
		for ($i = count($k); $i < 4; $i++) {
			$k[$i] = 0;
		}
	}
	$n = count($v) - 1;
	$z = $v[$n];
	$y = $v[0];
	$delta = 0x9E3779B9;
	$q = floor(6 + 52 / ($n + 1));
	$sum = int32($q * $delta);
	while ($sum != 0) {
		$e = $sum >> 2 & 3;
		for ($p = $n; $p > 0; $p--) {
			$z = $v[$p - 1];
			$mx = int32((($z >> 5 & 0x07ffffff) ^ $y << 2) + (($y >> 3 & 0x1fffffff) ^ $z << 4)) ^ int32(($sum ^ $y) + ($k[$p & 3 ^ $e] ^ $z));
			$y = $v[$p] = int32($v[$p] - $mx);
		}
		$z = $v[$n];
		$mx = int32((($z >> 5 & 0x07ffffff) ^ $y << 2) + (($y >> 3 & 0x1fffffff) ^ $z << 4)) ^ int32(($sum ^ $y) + ($k[$p & 3 ^ $e] ^ $z));
		$y = $v[0] = int32($v[0] - $mx);
		$sum = int32($sum - $delta);
	}
	return long2str($v, true);
}

function int32($n) {
	while ($n >= 2147483648) $n -= 4294967296;
	while ($n <= -2147483649) $n += 4294967296;
	return (int)$n;
}

function ew_UrlEncode($string) {
	$data = base64_encode($string);
	return str_replace(array('+','/','='), array('-','_','.'), $data);
}

function ew_UrlDecode($string) {
	$data = str_replace(array('-','_','.'), array('+','/','='), $string);
	return base64_decode($data);
}



function rand_str($length = 32, $chars = 'abcdefghijklmnopqrstuvwxyz1234567890')
{
    // Length of character list
    $chars_length = (strlen($chars) - 1);

    // Start our string
    $string = $chars{rand(0, $chars_length)};
   
    // Generate random string
    for ($i = 1; $i < $length; $i = strlen($string))
    {
        // Grab a random character from our list
        $r = $chars{rand(0, $chars_length)};
       
        // Make sure the same two characters don't appear next to each other
        if ($r != $string{$i - 1}) $string .=  $r;
    }
   
    // Return the string
    return $string;
}

function rand_int(){
	
	return rand(1000000,9999999);
	
	}


function check_rmtp_url($url){

		$slashes_number = substr_count($url, '/');
		
		$string = $url;

		$nthtimes = 4;
		
		$replaceme = '/';
		
		if($slashes_number==5){
			//echo "stringa modificata<br>";
			
			$pos_4 = strnpos($url, $replaceme, $nthtimes);
			
			//echo "posizione  $nthtimes slashes : $pos_4<br>";

			$new_url = substr($url, 0, $pos_4).'/'.substr($url, -(strlen($url)-$pos_4));
			
			return $new_url;

			
			
			
		
		}
		
		return $url;


}


function strnpos($haystack, $needle, $nth, $offset = 0)
{
    for ($retOffs=$offset-1; ($nth>0)&&($retOffs!==FALSE); $nth--) $retOffs = strpos($haystack, $needle, $retOffs+1);
    return $retOffs;
} 

	function debug($text){
		
		
			if (defined("EW_DEBUG_ENABLED"))  echo($text."<br>");

		
		}
function debug_var($var,$title = ""){
		
		
			if (defined("EW_DEBUG_ENABLED"))  echo("$title<pre>");
			if (defined("EW_DEBUG_ENABLED"))  var_dump($var);
			if (defined("EW_DEBUG_ENABLED"))  echo("</pre><br>");

		
		}
		
		
		
	function debug_die($msg = ""){ if (defined("EW_DEBUG_ENABLED")) die ("<strong>$msg</strong>");}
		


	function setMessage($v) {
		if (@$_SESSION[EW_SESSION_MESSAGE] <> "") { // Append
			$_SESSION[EW_SESSION_MESSAGE] .= "<br>" . $v;
		} else {
			$_SESSION[EW_SESSION_MESSAGE] = $v;
		}
	}
	
	// Show Message
	function printMessage() {
		if ($_SESSION[EW_SESSION_MESSAGE] <> "") { // Message in Session, display
			echo "<p><span class=\"ewMessage\">" . $_SESSION[EW_SESSION_MESSAGE] . "</span></p>";
			$_SESSION[EW_SESSION_MESSAGE] = ""; // Clear message in Session
		}
	}
		

?>
