$(document).ready(function () {
  function validateBeforeSubmit(event) {
    event.preventDefault();
    $(this).find('#device-name-input-message').empty();
    axios.get('/api/devices')
    .then((response) => {
      var deviceName = $(this).find('#device-name-input').val();
      var devices = response.data.payload.discoveredAppliances;

      for (let i=0; i<devices.length; ++i) {
        if (deviceName == devices[i].friendlyName) {
          $(this).find('#device-name-input-message').text("Device with '" + deviceName + "' already exists!");
          return false;
        }
      }

      this.submit();
    })
    .catch(function (error) {
      console.log(error);
    });
  }

  $('#device-add-form').submit(validateBeforeSubmit);
  $('#device-update-form').submit(validateBeforeSubmit);
});
