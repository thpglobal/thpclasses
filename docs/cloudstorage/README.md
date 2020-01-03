# CloudStorage Class - Under Construction
* This consolidates access our ability to upload, download or show files in google cloud storage under php7
* This class is not automatically loaded when you include thp_classes - it gets required separately as it has different dependencies than the other classes.
* It will only work if you've run composer require google/storage-client in your deployment shell base folder first.
## Public Methods
* **CloudStorage::start()** establishes the gs:// stream wrapper for google cloud storage.
* **CloudStorage::show($fullpath)** shows a pdf on the screen
* **CloudStorage::upload($sourcepath, $destpath)** uploads a file
* **CloudStorage::download($fullpath)** downloads a file to the user's computer
