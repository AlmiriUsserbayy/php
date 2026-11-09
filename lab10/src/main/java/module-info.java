module com.example.lab10_validator {
    requires javafx.controls;
    requires javafx.fxml;


    opens com.example.lab10_validator to javafx.fxml;
    exports com.example.lab10_validator;
}