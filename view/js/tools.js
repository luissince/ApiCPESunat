function Tools() {

    this.validateDate = function (date) {
        var regex = new RegExp("([0-9]{4}[-](0[1-9]|1[0-2])[-]([0-2]{1}[0-9]{1}|3[0-1]{1})|([0-2]{1}[0-9]{1}|3[0-1]{1})[-](0[1-9]|1[0-2])[-][0-9]{4})");
        return regex.test(date);
    }

    this.validateComboBox = function (comboBox) {
        if (comboBox.children('option').length == 0) {
            return true;
        }
        if (comboBox.children('option').length > 0 && comboBox.val() == "") {
            return true;
        } else {
            return false;
        }
    }

    this.validateEmail = function (value) {
        var validRegex = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        if (value.match(validRegex)) {
            return true;
        } else {
            return false;
        }
    }

    this.getDateYYMMDD = function (value) {
        var parts = value.split("-");
        return parts[0] + parts[1] + parts[2];
    }

    this.formatMoney = function (amount, decimalCount = 2, decimal = ".", thousands = "") {
        try {
            decimalCount = Math.abs(decimalCount);
            decimalCount = isNaN(decimalCount) ? 2 : decimalCount;

            const negativeSign = amount < 0 ? "-" : "";

            let i = parseInt(amount = Math.abs(Number(amount) || 0).toFixed(decimalCount)).toString();
            let j = (i.length > 3) ? i.length % 3 : 0;

            return negativeSign + (j ? i.substr(0, j) + thousands : '') + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" +
                thousands) + (decimalCount ? decimal + Math.abs(amount - i).toFixed(decimalCount).slice(2) : "");
        } catch (e) {
            return 0;
        }
    };

    this.getDateForma = function (value) {
        var parts = value.split("-");
        let today = new Date(parts[0], parts[1] - 1, parts[2]);
        return (
            (today.getDate() > 9 ? today.getDate() : "0" + today.getDate()) +
            "/" +
            (today.getMonth() + 1 > 9 ?
                today.getMonth() + 1 :
                "0" + (today.getMonth() + 1)) +
            "/" +
            today.getFullYear()
        );
    };

    this.getTimeForma = function (value, option) {
        let ar = value.split(":");
        let hr = ar[0];
        let min = parseInt(ar[1]);
        let arsec = ar[2].split(".");
        let sec = parseInt(arsec[0]);
        if (sec < 10) {
            sec = "0" + sec;
        }
        if (min < 10) {
            min = "0" + min;
        }
        let ampm = "am";
        if (hr > 12) {
            hr -= 12;
            ampm = "pm";
        }
        return option ? (hr > 9 ? hr : "0" + hr) + ":" + min + ":" + sec + " " + ampm : hr + ":" + min + ":" + sec;
    };

    this.getTimeForma24 = function (value) {
        var hourEnd = value.indexOf(":");
        var H = +value.substr(0, hourEnd);
        var h = H % 12 || 12;
        var ampm = (H < 12 || H === 24) ? "AM" : "PM";
        return h + value.substr(hourEnd, 3) + ":" + value.substr(6, 2) + " " + ampm;
    };

    this.getCurrentDate = function () {
        let today = new Date();
        let formatted_date = today.getFullYear() + "-" + ((today.getMonth() + 1) > 9 ? (today.getMonth() + 1) : '0' + (
            today.getMonth() + 1)) + "-" + (today.getDate() > 9 ? today.getDate() : '0' + today.getDate());
        return formatted_date;
    };

    this.getCurrentTime = function () {
        let today = new Date();
        let formatted_time = (today.getHours() > 9 ? today.getHours() : '0' + today.getHours()) + ":" + (today.getMinutes() > 9 ? today.getMinutes() : '0' + today.getMinutes()) + ":" + (today.getSeconds() > 9 ? today.getSeconds() : '0' + today.getSeconds());
        return formatted_time;
    }

    this.getCurrentMonth = function () {
        let today = new Date();
        return (today.getMonth() + 1);
    }

    this.getCurrentYear = function () {
        let today = new Date();
        return today.getFullYear();
    }

    this.getFirstDayOfMoth = function () {
        let date = new Date();
        let today = new Date(date.getFullYear(), date.getMonth(), 1);
        let formatted_date = today.getFullYear() + "-" + ((today.getMonth() + 1) > 9 ? (today.getMonth() + 1) : '0' + (
            today.getMonth() + 1)) + "-" + (today.getDate() > 9 ? today.getDate() : '0' + today.getDate());
        return formatted_date;
    }

    this.getLastDayOfMonth = function () {
        let date = new Date();
        let today = new Date(date.getFullYear(), date.getMonth() + 1, 0);
        let formatted_date = today.getFullYear() + "-" + ((today.getMonth() + 1) > 9 ? (today.getMonth() + 1) : '0' + (
            today.getMonth() + 1)) + "-" + (today.getDate() > 9 ? today.getDate() : '0' + today.getDate());
        return formatted_date;
    }

    this.diasEnUnMes = function (mes, year) {
        mes = mes.toUpperCase();
        var meses = ["ENERO", "FEBRERO", "MARZO", "ABRIL", "MAYO", "JUNIO", "JULIO", "AGOSTO", "SEPTIEMBRE", "OCTUBRE", "NOVIEMBRE", "DICIEMBRE"];
        return new Date(year, meses.indexOf(mes) + 1, 0).getDate();
    }

    this.nombreMes = function (mes) {
        let array = [
            "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio",
            "Julio", "Agosto", "Setiembre", "Octubre", "Noviembre", "Diciembre"
        ];
        return array[mes - 1];
    }

    this.isNumeric = function (value) {
        if (value.trim().length === 0 || value === 'undefined')
            return false;

        if (isNaN(value.trim())) {
            return false;
        } else {
            return true;
        }
    };

    this.isText = function (value) {
        if (value.trim() == "" || value.trim().length == 0 || value == 'undefined' || value == null) {
            return false;
        }
        return true;
    }

    this.getExtension = function (filename) {
        return filename.split("?")[0].split("#")[0].split('.').pop();
    }

    this.loadTable = function (tbody, colspan) {
        tbody.empty();
        tbody.append(`<tr><td class="text-center" colspan="${colspan}"><img src="./images/loading.gif" id="imgLoad" width="34" height="34" /> <p>Cargando información...</p></td></tr>`);
    }

    this.loadTableMessage = function (tbody, message, colspan, clear = false) {
        if (clear) {
            tbody.empty();
        }
        tbody.append(`<tr><td class="text-center" colspan="${colspan}"><p>${message}</p></td></tr>`);
    }

    this.paginationEmpty = function (component) {
        component.html(`
        <button class="btn btn-outline-secondary">
            <i class="fa fa-angle-double-left"></i>
        </button>
        <button class="btn btn-outline-secondary">
            <i class="fa fa-angle-left"></i>
        </button>
        <span class="btn btn-outline-secondary disabled" id="lblPaginacion">0 - 0</span>
        <button class="btn btn-outline-secondary">
            <i class="fa fa-angle-right"></i>
        </button>
        <button class="btn btn-outline-secondary">
            <i class="fa fa-angle-double-right"></i>
        </button>
    `);
    }

    this.messageError = function (message) {
        if (message.status == 404) {
            return "No se puedo encontrar la ruta solicitada.";
        }
        if (message.responseText == "" || message.responseText == null || message.responseText == "undefined" || message.responseText == undefined) {
            return "Se produjo un error interno, intente nuevamente por favor.";
        } else {
            if (message.responseJSON == "" || message.responseJSON == null || message.responseJSON == "undefined" || message.responseJSON == undefined) {
                return message.responseText;
            } else {
                return message.responseJSON;
            }
        }
    }

    this.ErrorMessageServer = function (title, message) {
        if (message.status == 404) {
            this.ModalAlertError(title, "No se puedo encontrar la ruta solicitada.");
        } else {
            if (message.responseText == "" || message.responseText == null || message.responseText == "undefined" || message.responseText == undefined) {
                this.ModalAlertError(title, "Se produjo un error interno, intente nuevamente por favor.");
            } else {
                if (message.responseJSON == "" || message.responseJSON == null || message.responseJSON == "undefined" || message.responseJSON == undefined) {
                    this.ModalAlertWarning(title, message.responseText);
                } else {
                    this.ModalAlertWarning(title, message.responseJSON);
                }
            }
        }
    }

    this.keyEnter = function (object, callback) {
        object.keypress(function (event) {
            if (event.keyCode === 13) {
                callback();
                event.preventDefault();
            }
        });
    }

    this.keyNumberFloat = function (object) {
        object.keypress(function (event) {
            var key = window.Event ? event.which : event.keyCode;
            var c = String.fromCharCode(key);
            if ((c < '0' || c > '9') && (c != '\b') && (c != '.')) {
                event.preventDefault();
            }
            if (c == '.' && $(this).val().includes(".")) {
                event.preventDefault();
            }
        })
    }

    this.keyNumberInteger = function (object) {
        object.keypress(function (event) {
            var key = window.Event ? event.which : event.keyCode;
            var c = String.fromCharCode(key);
            if ((c < '0' || c > '9') && (c != '\b')) {
                event.preventDefault();
            }
        })
    }

    this.limitar_cadena = function (cadena, limite, sufijo) {
        if (cadena.length > limite) {
            return cadena.substr(0, limite) + sufijo;
        }
        return cadena;
    }

    this.calculateTaxBruto = function (impuesto, monto) {
        return monto / ((impuesto + 100) * 0.01);
    }

    this.calculateTax = function (porcentaje, valor) {
        let igv = (parseFloat(porcentaje) / 100.00);
        return valor * igv;
    }

    this.calculateAumento = function (margen, costo) {
        let totalimporte = parseFloat(costo) + (parseFloat(costo) * (parseFloat(margen) / 100.00));
        return parseFloat(this.formatMoney(totalimporte, 1));
    }

    this.formatNumber = function (numeracion, length = 6) {
        if (numeracion.length >= 6) {
            return numeracion;
        }
        let pad_char = '0';
        let pad = new Array(1 + length).join(pad_char);
        return (pad + numeracion).slice(-pad.length);
    }

    this.ModalDialogInputText = function (title, mensaje, callback) {
        swal({
            title: title,
            text: mensaje,
            input: 'text',
            inputPlaceholder: 'Ingrese un el motivo de la anulación',
            type: 'question',
            showCancelButton: true,
            confirmButtonText: "Si",
            cancelButtonText: "No",
            allowOutsideClick: false,
        }).then((isConfirm) => {
            callback(isConfirm)
        });
    }

    this.ModalDialog = function (title, mensaje, callback) {
        swal({
            title: title,
            text: mensaje,
            type: 'question',
            showCancelButton: true,
            confirmButtonText: "Si",
            cancelButtonText: "No",
            allowOutsideClick: false
        }).then((isConfirm) => {
            if (isConfirm.value == undefined) {
                return false;
            }
            if (isConfirm.value) {
                callback(true)
            } else {
                callback(false)
            }
        });
    }

    this.ModalAlertSuccess = function (title, message, callback = function () { }) {
        swal({
            title: title,
            text: message,
            type: "success",
            showConfirmButton: true,
            allowOutsideClick: false
        }).then(() => {
            callback()
        });;
    }

    this.ModalAlertWarning = function (title, message) {
        swal({ title: title, text: message, type: "warning", showConfirmButton: true, allowOutsideClick: false });
    }

    this.ModalAlertError = function (title, message) {
        swal({ title: title, text: message, type: "error", showConfirmButton: true, allowOutsideClick: false });
    }

    this.ModalAlertInfo = function (title, message) {
        swal({ title: title, text: message, type: "info", showConfirmButton: false, allowOutsideClick: false, allowEscapeKey: false, });
    }

    this.AlertSuccess = function (title = "", message, position = "top", align = "right") {
        $.notify({
            title: title,
            message: message
        }, {
            type: 'success',
            placement: {
                from: position,
                align: align
            },
            z_index: 2000,
        });
    }

    this.AlertWarning = function (title = "", message, position = "top", align = "right") {
        $.notify({
            title: title,
            message: message
        }, {
            type: 'warning',
            placement: {
                from: position,
                align: align
            },
            z_index: 2000,
        });
    }

    this.AlertError = function (title = "", message, position = "top", align = "right") {
        $.notify({
            title: title,
            message: message
        }, {
            type: 'error',
            placement: {
                from: position,
                align: align
            },
            z_index: 2000,
        });
    }

    this.AlertInfo = function (title = "", message, position = "top", align = "right") {
        $.notify({
            title: title,
            message: message
        }, {
            type: 'info',
            placement: {
                from: position,
                align: align
            },
            z_index: 2000,
        });
    }

    this.promiseFetchGet = function (url, data, beforeSend = function () { }) {
        return new Promise(function (resolve, reject) {
            $.ajax({
                url: url,
                method: "GET",
                data: data,
                beforeSend: beforeSend,
                success: function (result) {
                    resolve(result);
                },
                error: function (error) {
                    reject(error);
                }
            });
        });
    }

    this.promiseFetchPost = function (url, data, beforeSend = function () { }) {
        return new Promise(function (resolve, reject) {
            $.ajax({
                url: url,
                method: "POST",
                accepts: "application/json",
                contentType: "application/json; charset=utf-8",
                data: JSON.stringify(data),
                beforeSend: beforeSend,
                success: function (result) {
                    resolve(result);
                },
                error: function (error) {
                    reject(error);
                }
            });
        });
    }

}