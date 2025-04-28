<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Dts_user;
use App\Models\Specialization;

class SpecializationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = Dts_user::where('section', 80)
                     ->get();

        $healthITRoles = [
            'Skilled IT Technician supporting IHOMIS (Integrated Hospital Operations Management Information System) to improve hospital workflows and patient management.',
            'Experienced IT Technician managing the eReferral system to ensure efficient patient referral management across healthcare facilities.',
            'IT Technician supporting the development and maintenance of electronic health record (EHR) systems in a fast-paced healthcare environment.',
            'Systems Administrator managing hospital IT infrastructure, ensuring seamless integration of health management systems and reliable network performance.',
            'IT Support Technician providing technical support and troubleshooting for healthcare professionals using clinical software and hospital systems.',
            'Network Administrator responsible for maintaining the network infrastructure of healthcare institutions, ensuring secure communication between systems.',
            'Database Administrator supporting the management of hospital databases, ensuring accuracy and compliance with healthcare standards.',
            'IT Technician providing hardware support for medical equipment, ensuring all devices and systems are functioning optimally for healthcare professionals.',
            'Printer Technician specializing in troubleshooting and repairing printers used in hospitals and healthcare facilities to ensure uninterrupted medical workflows.',
            'Network Support Technician maintaining and troubleshooting network systems across hospital facilities to ensure reliable communication and data flow.',
            'Infrastructure Technician supporting the setup, maintenance, and troubleshooting of healthcare IT infrastructure, including servers, workstations, and peripherals.',
            'Maintenance Technologist specializing in the upkeep and performance of hospital IT systems and medical devices, ensuring optimal functionality and uptime.',
            'Preventive Maintenance Technician ensuring regular maintenance and upgrades for healthcare IT systems, minimizing downtime and maximizing system efficiency.',
            'Corrective Maintenance Technician handling IT system repairs and troubleshooting to address any technical failures in the hospital network and hardware.',
            'IT Implementation Specialist overseeing the implementation of new healthcare IT systems, ensuring smooth system integrations and configurations.',
            'Healthcare IT Implementation Consultant managing the deployment of hospital IT systems such as EHR, patient management, and diagnostic tools.',
            'Field Service Technician providing on-site support for healthcare IT infrastructure, handling emergency repairs, installations, and system upgrades.',
            'IT Systems Support Specialist responsible for the troubleshooting, installation, and maintenance of medical equipment and IT systems across healthcare facilities.',
            'Network Installation Technician focusing on the installation, testing, and maintenance of hospital network systems, ensuring secure and reliable communication.',
            'IT Operations Technician responsible for the daily operations and management of IT infrastructure in healthcare institutions, ensuring system performance and security.',
            'Healthcare Technology Support Specialist troubleshooting and providing solutions for healthcare professionals dealing with technical issues in clinical software and hardware.',
        ];

        // Assign a specialization to each user (Technician)
        foreach ($users as $user) {
            // Generate a random specialization for each technician (ensure each gets a unique one)
            $specialization = $healthITRoles[array_rand($healthITRoles)];

            // Insert the specialization for each user
            Specialization::create([
                'userid' => $user->username,
                'specialization' => $specialization,
            ]);
        }
    }
}
