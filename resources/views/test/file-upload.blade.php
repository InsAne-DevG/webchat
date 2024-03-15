<form action="" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="file" name="files[]" accept="image/*">
    <input type="file" name="files[]" accept="image/*">
    <input type="file" name="files[]" accept="image/*">
    <input type="file" name="files[]" accept="image/*">

    <button type="submit">Upload</button>
</form>
