<?php 
	header("Access-Control-Allow-Origin: *");
	header("Access-Control-Allow-Headers: *");

	// Allow from any origin
    if (isset($_SERVER['HTTP_ORIGIN'])) {
        // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
        // you want to allow, and if so:
        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');    // cache for 1 day
    }

    // Access-Control headers are received during OPTIONS requests
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
            // may also be using PUT, PATCH, HEAD etc
            header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
            header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

        exit(0);
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Clever - Bootstrap 4 Admin Template">
    <meta name="author" content="Łukasz Holeczek">
    <meta name="keyword" content="Bootstrap,Admin,Template,Open,Source,AngularJS,Angular,Angular2,jQuery,CSS,HTML,RWD,Dashboard,Vue,Vue.js,React,React.js">
    <link rel="shortcut icon" href="img/favicon.png">

    <title>MTM Box</title>

    <!-- Icons -->
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="css/simple-line-icons.css" rel="stylesheet">

    <!-- Main styles for this application -->
    <link href="css/style.css" rel="stylesheet">

</head>

<body class="app header-fixed sidebar-fixed aside-menu-fixed aside-menu-hidden">
    
    <div class="justify-content-center row">
        <h1 class="display-6">MTM Box</h1><br>
    </div>
    <div class="justify-content-center row">
        <br><br>
        <div class="col-sm-9 col-lg-9 ">
            <div class="card card-inverse card-info">
                <div class="card-header">Upload New File</div>
                <div class="card-block">
                <fieldset class="form-group">
                    <label>Chose a file</label>
                    <div class="input-group">
                        <input type="file" value="upload" id="fileButton"/>
                    </div>
                    <br>
                    <div class="progress">
                        <div class="progress-bar progress-bar-striped bg-success progress-bar-animated" role="progressbar" style="width: 0%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100" id="uploader"></div>
                    </div> 
                </fieldset>
                </div>
            </div>
        </div>
    </div>
    <div class="justify-content-center row">
        <div class="col-sm-9 col-lg-9">
            <div class="card card-inverse card-primary">
                <div class="card-header">File Explorer</div>
                <div class="card-block">
                    <ul id="list" class="fileExplorer list-unstyled"></ul>
                </div>
            </div>
        </div>
    </div>
    <style>
        .file{
            cursor: pointer; 
            cursor: hand;
        }
    </style>

    <!-- Bootstrap and necessary plugins -->
    <script src="js/libs/jquery.min.js"></script>
    <script src="js/libs/tether.min.js"></script>
    <script src="js/libs/bootstrap.min.js"></script>
    <script src="js/libs/pace.min.js"></script>

    <!-- FireBase -->
    <script src="https://www.gstatic.com/firebasejs/4.1.3/firebase.js"></script>

    <!-- Start Global Variables -->
    <script>
        // Initialize Firebase
        var config = {
            apiKey: "AIzaSyCo0cTqIqq8MgAtdVscPqTbZAakA2T9etM",
            authDomain: "mtm-box.firebaseapp.com",
            databaseURL: "https://mtm-box.firebaseio.com",
            projectId: "mtm-box",
            storageBucket: "mtm-box.appspot.com",
            messagingSenderId: "56184530107"
        };
        firebase.initializeApp(config);

        // List Files
        var ulList = document.getElementById('list');

        var dbfile = firebase.database().ref().child('file');

        dbfile.on('child_added', snap => {
            var li = document.createElement('li');
            li.innerText = snap.val().name;
            li.setAttribute("url", snap.val().url);
            li.setAttribute("size", snap.val().size);
            li.setAttribute("class", 'file');
            ulList.appendChild(li);
        });
        $('ul.fileExplorer').on('click','li.file', function(e){
            // Download Directly
            var xhr = new XMLHttpRequest();
            xhr.responseType = 'blob';
            xhr.onload = function(event) {
                var blob = xhr.response;
            };
            xhr.open('GET', $(this).attr('url'));
            xhr.send();
        });

        // Store new File
        // Get Elements
        var uploader = document.getElementById('uploader');
        var fileButton = document.getElementById('fileButton');

        fileButton.addEventListener('change', function(e){
            // Get elements
            var uploader = document.getElementById('uploader');
            var file = e.target.files[0];

            // Create Storage Ref
            var storageRef = firebase.storage().ref('file/' + file.name);

            var task = storageRef.put(file);

            // Update progress bar
            task.on('state_changed', 

                function progress(snapshot){
                    var percentage = (snapshot.bytesTransferred/snapshot.totalBytes) * 100;
                    uploader.setAttribute('style', style="width: "+percentage+"%");
                    if(snapshot.downloadURL !== null) {
                        var ref = firebase.database().ref('file/' + file.name.substring(0, file.name.lastIndexOf('.')));
                        ref.set({
                            name: file.name,
                            size: file.size,
                            parent: 0,
                            url: snapshot.downloadURL
                        });
                    }
                },

                function error(err){
                    alert('Upload Failed!');
                    uploader.setAttribute('style', style="width: 0%");
                    fileButton.value = '';
                },

                function complete(){
                    alert('Upload Completed!');
                    uploader.setAttribute('style', style="width: 0%");
                    fileButton.value = '';
                }
            );
        });
    </script>

</body>

</html>