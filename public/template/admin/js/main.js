$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

function removeRow(id, url)
{
    if(confirm('Bạn có chắc xóa không')){
        $.ajax({
            type: 'DELETE',
            dataType: 'JSON',
            data: { id },
            url: url,
            success: function(result){
                if(result.error === false){
                    alert(result.message);
                    location.reload();
                }else{
                    alert('Xóa lỗi vui lòng sửa lại');
                }
            }
        })
    }
}


$('#upload').change(function (){
    const form = new FormData();
    form.append('file', $(this)[0].files[0]);

    $.ajax({
        processData: false,
        contentType: false,
        type: 'POST',
        dataType: 'JSON',
        data: form,
        url: '/upload/services',
        success: function(results){
            if(results.error === false){
                console.log('dung');
                $('#image_show').html('<a href="' + results.url + '" target="_blank"><img src="' + results.url + '" width="100px"></a>')

                $('#thumb').val(results.url);
            }else{
                console.log('sai');
                alert('Upload File Error');
            }
        }
    })
});

// $('#uploadMultiple').change(function (){
//     console.log('aa0');
//     const form = new FormData();
//     form.append('file', [$(this)[0].files[0],$this(this)[1].files[1]]);

//     $.ajax({
//         processData: false,
//         contentType: false,
//         type: 'POST',
//         dataType: 'JSON',
//         data: form,
//         url: 'upload/services/multiple',
//         success: function(results){
//             if(results.error === false){
//                 $('#image_show').html('<a href="' + results.url + '" target="_blank"><img src="' + results.url + '" width="100px"></a>')

//                 $('#thumb').val(results.url);
//             }else{
//                 alert('Upload File Error');
//             }
//         }
//     })
// });