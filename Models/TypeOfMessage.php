<?php
	enum TypeOfMessage : string
	{
		case SUCCESS = "";
		case ERROR = "Chyba: ";
		case WARNING = "Varování: ";
		case INFO = "Informační sdělení: ";
	}