🏢 Resident Billing System

This project is a **Resident Billing System** designed to manage utility billing for apartment residents within a residential building setup. It streamlines the process of recording meter readings, generating bills, applying penalties, managing payments, and keeping track of all administrative and resident-related information.
 📊 Entity Relationship :

The system is based on a robust relational database model that includes the following key entities:

* **Admin**: Manages system access and configurations.
* **Blocks & Apartments**: Represents the physical layout of the residential area.
* **Residents**: Contains personal and contact details of individuals living in the apartments.
* **Meter Readings**: Records monthly utility consumption for billing.
* **Bill & Bill Types**: Stores billing details and categorizes them by utility type.
* **Tariffs**: Holds rate information for different billing types effective from specific dates.
* **Penalties**: Applied to overdue bills with calculated amounts and dates.
* **Payments**: Tracks all resident payments including payment mode and date.
* **Notices**: Used to send important messages or announcements to targeted audiences.
* **Vendors**: Maintains service providers related to the building (e.g., electricity, gas).
* **Roles**: For potential role-based access management.

 ⚙️ Key Features

* Automated billing calculation based on meter readings and tariff rates.
* Penalty enforcement on late payments.
* Payment tracking and reconciliation.
* Admin notice management for residents.
* Modular and scalable database schema.


