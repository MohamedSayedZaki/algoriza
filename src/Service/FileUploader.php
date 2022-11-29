<?php

namespace App\Service;

use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader{

    private $slugger = '';
    private $targetDirectory ='';

    public function __construct($targetDirectory, SluggerInterface $slugger){
        $this->slugger = $slugger;
        $this->targetDirectory = $targetDirectory;
    }

    public function upload(UploadedFile $file){
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

        try {
            $file->move($this->targetDirectory, $fileName);
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
        }

        return $fileName;
    }

    public function remove($file):bool
    {
        $filePath = $this->targetDirectory.'/'.$file;
        if (file_exists($filePath)){
            unlink($filePath);
        }

        return true; 
    }
}