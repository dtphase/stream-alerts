<html>
   <body>

      @php
         echo Form::open(array('url' => '/uploadfile','files'=>'true'));
         echo 'Select the file to upload.';
         echo Form::file('json');
         echo Form::number('id', '1');
         echo Form::submit('Upload File');
         echo Form::close();
      @endphp

   </body>
</html>
