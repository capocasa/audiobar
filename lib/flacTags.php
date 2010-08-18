<?php
/**
* Copyright (c) 2008 - Lorenzo Simionato
* All rights reserved.   This program and the accompanying materials
* are made available under the terms of the
* GNU General Public License (GPL) Version 2, June 1991,
* which accompanies this distribution, and is available at:
* http://www.opensource.org/licenses/gpl-license.php
*
*
* flacTags:
* A simple class that parses FLAC files.
* It can retrive easily the Vorbis Comment Data Block
* So you can get infos like Title, Author, Album, Comments...
* from any .flac file
*
*
* Date: October 2008
* Version: 0.2.1
* Author: Lorenzo Simionato
* Email: lorenzo [AT] simionato [dot] org
**/

class flacTags {

  var $file_name;    // File name
  var $v_string;     // Vendor string
  var $comments;     // Comments array
  var $errorMessage; // Error message

  // CONSTRUCTOR
  function flacTags($inputfile) {
    $this->file_name = $inputfile;
    $this->errorMessage="";
    $this->v_string="";
    $this->comments=null;
  }

  // READ THE FILE
  // return false if an error occured
  function readTags() {
    $file = fopen($this->file_name, "rb");
    if($file == false) {
      $this->errorMessage="Error opening file";
      return false;
    }

    // Check if is a FLAC file
    if(fread($file,4)!="fLaC") {
      $this->errorMessage="Not valid FLAC file";
      return false;
    }

    // Search for the VORBIS_COMMENT BLOCK
    $found=false;
    $last=false;
    while(!$found && !$last) {
      $block=fread($file,1); //get block header
      if($block==false) {
        $this->errorMessage="Not valid FLAC file";
        return false;
      }
      $block=ord($block);

      if($block & 128)  // if first bit=1, this is the last block
        $last=true;
      $block=$block & 127; // get last 7 bits

      if($block==4) // if 4, this is the vorbis comment block
        $found=true;

      $sizes=fread($file,3); //get size of the block
      $size=ord($sizes[0]) << 16;
      $size+=ord($sizes[1]) << 8;
      $size+=ord($sizes[2]);

      if(!$found)
        fseek($file,$size,SEEK_CUR);
    }

    if(!$found) {
      $this->errorMessage="Vorbis comment not found";
      return false;
    }

    //Retrive vorbis block 

    //get vendor string size
    $vsize=fread($file,4);
    $vsize=$this->convert($vsize);

    $vstr=fread($file,$vsize);
    if($vstr==false) {
      $this->errorMessage="Not valid FLAC file";
      return false;
    }
    $this->v_string=$vstr;

    //get numbers of comments
    $csize=fread($file,4);
    $csize=$this->convert($csize);

    //read comments
    for($i=0;$i<$csize;$i++) {
      //get comment length
      $cosize=fread($file,4);
      $cosize=$this->convert($cosize);

      //get comment value
      $comment=fread($file,$cosize);
      $pos=strpos($comment,"=");
      if($pos==false) {
        $this->errorMessage="Not valid FLAC file";
        return false;
      }

      $field=substr($comment,0,$pos);
      $value=substr($comment,$pos+1);

      if(!isset($this->comments[$field]))
        $this->comments[$field]=$value;
      else if(is_array($this->comments[$field]))
        $this->comments[$field][count($this->comments[$field])]=$value;
      else {
        $temp=$this->comments[$field];
        $this->comments[$field]=array();
        $this->comments[$field][0]=$temp;
        $this->comments[$field][1]=$value;
      }
    }

    fclose($file);
    return true;
  }

  // get a comment
  function getComment($name) {
    return $this->comments[$name];
  }

  // get an array with all comments
  function getAllComments() {
    return $this->comments;
  }

  // get the vendor string
  function getVendorString() {
    return $this->v_string;
  }

  // get the error message
  function getError() {
    return $this->errorMessage;
  }

  // Internal function
  function convert($s) {
    $size=ord($s[0]);
    $size+=ord($s[1]) << 8;
    $size+=ord($s[2]) << 16;
    $size+=ord($s[3]) << 24;
    return $size;
  }
}