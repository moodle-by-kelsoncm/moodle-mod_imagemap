Backup and restore
==================

Image maps are included in course backups, imports and in the duplication of activities, with their image, description files, areas and lines.

* When the whole course is restored, each area points to the copy of its activity or section in the new course, whatever their order in the course.
* When only the image map is restored or duplicated, areas that point to items of the same course keep them; areas that point to items outside the backup lose their target and must be linked again.
* The CSS of the areas is cleaned again on restore (see :doc:`configuration`).
* The CSS examples are site settings: they are not included in backups and a restore never changes them. Each area keeps its own CSS, so a restored image map looks the same without them. To copy the examples to another site, use the export and import of the examples (see :doc:`configuration`).

Privacy
-------

The plugin stores no personal data: areas, lines and CSS examples are course and site content. It implements the Privacy API as a null provider.
