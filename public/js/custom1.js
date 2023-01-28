$(document).ready(function() {
    // Check admin password is correct or not
    $('#current_password').keyup(function() {
        var current_password = $('#current_password').val();
        // alert(current_password); 
        $.ajax({
            headers: { 
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') 
            },
            type:'post',
            url:'/admin/check-admin-password',
            data:{current_password:current_password},
            success:function(resp) {
                // alert(resp);
                if(resp == 'false') {
                    $('#check_password').html("<front class='text-danger'>Current Password is Incorrect!</front>");
                } else if(resp == 'true') {
                    $('#check_password').html("<front class='text-success'>Current Password is Correct!</front>");
                }
            }, error:function() {
                alert('Error');
            }
        });
    });

    $('#profile_current_password').keyup(function() {
        var profile_current_password = $('#profile_current_password').val();
        // alert(current_password); 
        $.ajax({
            headers: { 
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') 
            },
            type:'post',
            url:'/check-user-password',
            data:{profile_current_password:profile_current_password},
            success:function(resp) {
                // alert(resp);
                if(resp == 'false') {
                    $('#profile_check_password').html("<front class='text-danger'>Current Password is Incorrect!</front>");
                } else if(resp == 'true') {
                    $('#profile_check_password').html("<front class='text-success'>Current Password is Correct!</front>");
                }
            }
        });
    });

    // setTime Alert
    setTimeout(function(){
        $('.alert').fadeOut("slow");
    }, 3000);

    // Update Category Status
    $('.updateCategoryStatus').click(function() {
        var status = $(this).children("svg").attr("status");
        var category_id = $(this).attr("category_id");
        $.ajax({
            headers: { 
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') 
            },
            method:'POST',
            url:'/admin/update-category-status',
            data:{status:status, category_id:category_id},
            success:function(resp) {
                if(resp['status']==1) {
                    $("#category-"+category_id).html('<svg status="Inactive" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-bookmark" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M2 15.5V2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v13.5a.5.5 0 0 1-.74.439L8 13.069l-5.26 2.87A.5.5 0 0 1 2 15.5zm8.854-9.646a.5.5 0 0 0-.708-.708L7.5 7.793 6.354 6.646a.5.5 0 1 0-.708.708l1.5 1.5a.5.5 0 0 0 .708 0l3-3z"/></svg>');
                } else if(resp['status']==0) {
                    $("#category-"+category_id).html('<svg status="Active" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-bookmark-check-fill" viewBox="0 0 16 16"><path d="M2 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v13.5a.5.5 0 0 1-.777.416L8 13.101l-5.223 2.815A.5.5 0 0 1 2 15.5V2zm2-1a1 1 0 0 0-1 1v12.566l4.723-2.482a.5.5 0 0 1 .554 0L13 14.566V2a1 1 0 0 0-1-1H4z"/></svg>');
                }
            }, error:function() {
                // url:'all';
                alert("Error");
            }
        });
    });

    // Update Product Status
    $('.updateProductStatus').click(function() {
        var status = $(this).children("svg").attr("status");
        var product_id = $(this).attr("product_id");
        $.ajax({
            headers: { 
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') 
            },
            method:'POST',
            url:'/admin/update-product-status',
            data:{status:status, product_id:product_id},
            success:function(resp) {
                if(resp['status']==1) {
                    $("#product-"+product_id).html('<svg status="Inactive" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-bookmark" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M2 15.5V2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v13.5a.5.5 0 0 1-.74.439L8 13.069l-5.26 2.87A.5.5 0 0 1 2 15.5zm8.854-9.646a.5.5 0 0 0-.708-.708L7.5 7.793 6.354 6.646a.5.5 0 1 0-.708.708l1.5 1.5a.5.5 0 0 0 .708 0l3-3z"/></svg>');
                } else if(resp['status']==0) {
                    $("#product-"+product_id).html('<svg status="Active" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-bookmark-check-fill" viewBox="0 0 16 16"><path d="M2 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v13.5a.5.5 0 0 1-.777.416L8 13.101l-5.223 2.815A.5.5 0 0 1 2 15.5V2zm2-1a1 1 0 0 0-1 1v12.566l4.723-2.482a.5.5 0 0 1 .554 0L13 14.566V2a1 1 0 0 0-1-1H4z"/></svg>');
                }
            }, error:function() {
                // url:'all';
                alert("Error");
            }
        });
    });

    // Update Staff Status
    $('.updateStaffStatus').click(function() {
        var status = $(this).children("svg").attr("status");
        var staff_id = $(this).attr("staff_id");
        alert(staff_id);
        $.ajax({
            headers: { 
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') 
            },
            method:'POST',
            url:'/admin/update-staff-status',
            data:{status:status, staff_id:staff_id},
            success:function(resp) {
                if(resp['status']==1) {
                    $("#staff-"+staff_id).html('<svg status="Inactive" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-bookmark" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M2 15.5V2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v13.5a.5.5 0 0 1-.74.439L8 13.069l-5.26 2.87A.5.5 0 0 1 2 15.5zm8.854-9.646a.5.5 0 0 0-.708-.708L7.5 7.793 6.354 6.646a.5.5 0 1 0-.708.708l1.5 1.5a.5.5 0 0 0 .708 0l3-3z"/></svg>');
                } else if(resp['status']==0) {
                    $("#staff-"+staff_id).html('<svg status="Active" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-bookmark-check-fill" viewBox="0 0 16 16"><path d="M2 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v13.5a.5.5 0 0 1-.777.416L8 13.101l-5.223 2.815A.5.5 0 0 1 2 15.5V2zm2-1a1 1 0 0 0-1 1v12.566l4.723-2.482a.5.5 0 0 1 .554 0L13 14.566V2a1 1 0 0 0-1-1H4z"/></svg>');
                }
            }, error:function() {
                // url:'all';
                alert("Error");
            }
        });
    });


    $(".confirmDelete").click(function() {
        var module = $(this).attr('module');
        var moduleid = $(this).attr('moduleid');
        var modulename = $(this).attr('modulename');
        Swal.fire({
            title: 'Are you sure?',
            html: "<p>Delete <b>" + modulename + "</b></p> <p>You won't be able to revert this!</p>",
            icon: 'warning',
            showCancelButton: true,
            cancelButtonColor: '#3085d6',
            confirmButtonColor: '#d33',
            confirmButtonText: 'Confirm'
        }).then((result) => {
            if (result.isConfirmed) {
                // Swal.fire (
                //     'Deleted!',
                //     'modulename has been deleted.',
                //     'success'
                // )
                window.location = "/admin/delete-"+module+"/"+moduleid;
            }
        });
    });

    $(".confirmDeleteFront").click(function() {
        var module = $(this).attr('module');
        var moduleid = $(this).attr('moduleid');
        var modulename = $(this).attr('modulename');
        Swal.fire({
            title: 'Are you sure?',
            html: "<p>Delete <b>" + modulename + "</b></p> <p>You won't be able to revert this!</p>",
            icon: 'warning',
            showCancelButton: true,
            cancelButtonColor: '#3085d6',
            confirmButtonColor: '#d33',
            confirmButtonText: 'Confirm'
        }).then((result) => {
            if (result.isConfirmed) {
                // Swal.fire (
                //     'Deleted!',
                //     'modulename has been deleted.',
                //     'success'
                // )
                window.location = "/delete-"+module+"/"+moduleid;
            }
        });
    });

    $(".confirmDeleteAddress").click(function() {
        var module = $(this).attr('module');
        var moduleid = $(this).attr('moduleid');
        var modulename = $(this).attr('modulename');
        Swal.fire({
            title: 'Are you sure?',
            html: "<p>Delete <b>" + modulename + "</b></p> <p>You won't be able to revert this!</p>",
            icon: 'warning',
            showCancelButton: true,
            cancelButtonColor: '#3085d6',
            confirmButtonColor: '#d33',
            confirmButtonText: 'Confirm'
        }).then((result) => {
            if (result.isConfirmed) {
                // Swal.fire (
                //     'Deleted!',
                //     'modulename has been deleted.',
                //     'success'
                // )
                window.location = "/delete-"+module+"/"+moduleid;
            }
        });
    });

    $("#sortListing").on("change", function() {
        // this.form.submit();
        var sort = $("#sortListing").val();
        var url = $("#url").val();
        $.ajax({
            headers: { 
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') 
            },
            url:url,
            method:'POST',
            data:{sort:sort, url:url},
            success:function(data) {
                $('.filter_products').html(data);
            }, error:function() {
                // url:'all';
                alert("Error");
            }
        });
    });

    $("#sortListing1").on("change", function() {
        // this.form.submit();
        var sort = $("#sortListing").val();
        var url = '/all'
        $.ajax({
            headers: { 
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') 
            },
            url:url,
            method:'POST',
            data:{sort:sort, url:url},
            success:function(data) {
                $('.filter_products').html(data);
            }, error:function() {
                // url:'all';
                alert("Error");
            }
        });
    });

    $("#sort").on("change", function() {
        this.form.submit();
    });
    
    $("#address_city").on("change", function() {
        // this.form.submit();
        var address_city = $("#address_city").val();
        var url = '/address/get-districts/'+address_city;

        $('#address_district').html('');

        $.ajax({
            headers: { 
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') 
            },
            url: url,
            method:'POST',
            dataType: 'json',
            data:{address_city:address_city, url:url},
            success:function(data) {
                $('#address_district').html('<option value="">Select District</option>');
                $.each(data, function (key, value) {
                    $("#address_district").append('<option value="' + value
                        .id + '">' + value.name + '</option>');
                });
                $('#address_ward').html('<option value="">Select Ward</option>');
            }
        });
    });

    $("#address_district").on("change", function() {
        // this.form.submit();
        var address_district = $("#address_district").val();
        var url = '/address/get-wards/'+address_district;

        $('#address_ward').html('');
        
        $.ajax({
            headers: { 
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') 
            },
            url: url,
            method:'POST',
            dataType: 'json',
            data:{address_district:address_district, url:url},
            success:function(data) {
                $('#address_ward').html('<option value="">Select District</option>');
                $.each(data, function (key, value) {
                    $("#address_ward").append('<option value="' + value
                        .id + '">' + value.name + '</option>');
                });
            }
        });
    });

    $("#update_address_city").on("change", function() {
        // this.form.submit();
        var address_city = $("#update_address_city").val();
        var url = '/address/get-districts/'+address_city;

        $('#update_address_district').html('');

        $.ajax({
            headers: { 
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') 
            },
            url: url,
            method:'POST',
            dataType: 'json',
            data:{address_city:address_city, url:url},
            success:function(data) {
                $('#update_address_district').html('<option value="">Select District</option>');
                $.each(data, function (key, value) {
                    $("#update_address_district").append('<option value="' + value
                        .id + '">' + value.name + '</option>');
                });
                $('#update_address_ward').html('<option value="">Select Ward</option>');
            }
        });
    });

    $("#update_address_district").on("change", function() {
        // this.form.submit();
        var address_district = $("#update_address_district").val();
        var url = '/address/get-wards/'+address_district;

        $('#update_address_ward').html('');
        
        $.ajax({
            headers: { 
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') 
            },
            url: url,
            method:'POST',
            dataType: 'json',
            data:{address_district:address_district, url:url},
            success:function(data) {
                $('#update_address_ward').html('<option value="">Select District</option>');
                $.each(data, function (key, value) {
                    $("#update_address_ward").append('<option value="' + value
                        .id + '">' + value.name + '</option>');
                });
            }
        });
    });

    $("#search").keyup(function()  {
        var search = $(this).val();
        $.ajax({
            // headers: { 
            //     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') 
            // },
            type:'GET',
            url: 'search',
            data:{'search':search},
            success:function(data) {
                $('#search_list').html(data);
            }
        });
    });

    // Profile
    $(".emailCheck").click(function() {
        if (this.checked == true){
            $(".emailUpdateCheck").css("display","block");
            $(".currentPasswordUpdateCheck").css("display","block");
        } else {
            $(".emailUpdateCheck").css("display","none");
            $(".currentPasswordUpdateCheck").css("display","none");
        }
    });

    $(".passwordCheck").click(function() {
        if (this.checked == true){
            $(".passwordUpdateCheck").css("display","block");
            $(".currentPasswordUpdateCheck").css("display","block");
        } else {
            $(".passwordUpdateCheck").css("display","none");
            $(".currentPasswordUpdateCheck").css("display","none");
        }
    });

    $(".addressCheck").click(function() {
        if (this.checked == true){
            var address_id = $(this).attr("address_id");
            var addressCheck = $('.addressCheck').val();
            alert(addressCheck);
            $.ajax({
                headers: { 
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') 
                },
                type:'post',
                url:'/address-default',
                data:{addressCheck:addressCheck},
                success:function(resp) {
                    // alert(resp);
                    $('#addressDefault').html(resp);
                }, error:function() {
                    alert('Error');
                }
            });
        } 
        
    });

    
});