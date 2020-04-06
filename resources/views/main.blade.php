<!doctype html>
<html>
<meta name="viewport" content="width=device-width, initial-scale=1">

<head>
    <meta charset="utf-8">
    <meta name="description" content="">
    <meta name="author" content="Theo">

    <link rel="stylesheet" href="{{asset('css/app.css')}}">

<style>
    .top-text {
        padding-left: 1em;
    }
</style>
</head>

<div id="app">
<div class="container-fluid">
    <div class="row">
            <div class="col-md-12">
                    <b-card img-width="135px">
                        <b-row>
                            <b-col md="2">
                                <img width="135px" src="{{asset('img/myaElephant.png')}}"></img>
                            </b-col>
                            <b-col md="10">
                                <div class="top-text">
                                <h2>
                                    Core Archive Set Request Form
                                </h2>
                                <p>
                                    Use the forms below to request adding channels to the Mya archiver or to change deadbands or metadata of
                                    currently archived channels.  For other types of requests, please <a href="#">contact the Mya Administrator</a>.
                                </p>
                                <p>
                                    <a class="btn btn-primary btn-large" href="#">Learn more</a>
                                </p>
                                </div>
                            </b-col>
                        </b-row>
                    </b-card>
                </div>
    </div>


    <div class="row">
        <div class="col-md-12">
        <main-form>

        </main-form>
        </div>
    </div>

</div>
</div>

<script>
var archiverGroups = @json($groups, JSON_PRETTY_PRINT);
var groupTrees = @json($groupTrees, JSON_PRETTY_PRINT);
</script>

<script src="{{asset('js/app.js')}}"></script>

</html>

