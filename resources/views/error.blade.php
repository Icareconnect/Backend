<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<main>
  <div class="container">
    <div class="row">
      <div class="col-md-6 align-self-center">
        <h1>{{ $exception->getCode() }}</h1>
        <h2>UH OH! You're lost.</h2>
        <h4 >{{ $exception->getMessage() }}
        </h4>
        <a class="btn btn-info" href="{{ url('/') }}">Home</a>
      </div>
    </div>
  </div>
</main>
<style type="text/css">
  @import url('https://fonts.googleapis.com/css?family=Nunito+Sans');
:root {
  --blue: #0e0620;
  --white: #fff;
  --green: #2ccf6d;
}
html,
body {
  height: 100%;
}
body {
  display: flex;
  align-items: center;
  justify-content: center;
  font-family:"Nunito Sans";
  color: var(--blue);
  font-size: 1em;
}
button {
  font-family:"Nunito Sans";
}

h1 {
  font-size: 7.5em;
  margin: 15px 0px;
  font-weight:bold;
}
h2 {
  font-weight:bold;
}
</style>
