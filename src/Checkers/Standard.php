<?php

namespace nazmulpcc\Checkers;

class Standard extends BaseChecker
{
    public function check()
    {
        if(!$this->sourceFile || !$this->outputFile){
            throw new Exception("Befor checking, source and output must be set");
        }
        $presentation = false;
        if(static::$checkPresenation){
            $presentation = $this->checkPresentation();
            if(!$presentation){
                return Verdict::WRONG_ANSWER;
            }
        }
        $status = $this->diff($this->sourceFile, $this->outputFile);
        if(!$status){
            return $presentation ? Verdict::PRESENTATION_ERROR : Verdict::WRONG_ANSWER;
        }
        return Verdict::ACCEPTED;
    }

    /**
     * Check for a presentation error
     *
     * @return void
     */
    public function checkPresentation()
    {
        $src = $this->removeWhiteSpaces($this->sourceFile, $this->tmpPath());
        $out = $this->removeWhiteSpaces($this->outputFile, $this->tmpPath());
        $status = $this->diff($src, $out);
        $this->delete($src);
        $this->delete($out);
        return $status;
    }
}
