# Qualisys PAF AnyBody example

This example automatically generates AnyBody projects for IOR, CAST, and Sport full-body marker sets for walking gait analyses using two force plates. 
It prepares all necessary AnyBody scripts, copies all C3D files, establishes links between them, and also incorporates the weight and height of the subject. The project includes three full-body walking gait examples, each utilizing different marker sets. Once the project (IOR, CAST, or Sport) is generated, it can be run with the AnyBody Modeling software to produce the data. If the PAF AnyBody example is used with data without force plate, the script should be modified to be able to predict forces. This modification applies similarly to lower body analyses, running on a treadmill, etc. The project contains a 'Documentation' folder, which includes guides for IOR, CAST, and Sport marker sets.

## Getting started
To download this example project to your computer, you can either:

* [Click here](https://github.com/qualisys/paf-anybody-example/archive/refs/heads/main.zip) to download it as a zip file.
<br>_— or —_
* Clone this repository to your computer.

## Preparing Qualisys data for AnyBody processing
### Preparation
1. Downaload and install AnyBody Modeling System from https://www.anybodytech.com/resources/customer-downloads/ for exisiting customeres or request a trial version from https://www.anybodytech.com/resources/trial-license/
   Register the license (see the https://www.anybodytech.com/download/installguide-anybody/)
   - Getting started https://youtu.be/Y4H-8FMJlis with AnyBody videos on YouTube
   - E-learning https://www.youtube.com/user/anybodytech/videos videos on YouTube
   - Tutorials in AnyBody’s Help > Tutorials menu
   - Wiki and user forum on https://anyscript.org/
2. Open PAF AnyBody Example
   - Inside, you will find 3 walking gait examples for the IOR, CAST, and Sport marker sets. Open the '01 John Doe' tree, and then select '2023-11-14_IOR'.
   - Click 'Start Processing.' It will generate an IOR folder in the current session containing all the necessary AnyBody scripts for this session.
   - Locate the '2023-11-14_IOR' folder and right-click to select 'Open folder in explorer.'
   - You will find a folder named 'IOR' (for the CAST project, this folder would be named 'CAST,' and similarly for the Sport marker set).
3. Running AnyBody software
   1. Open the file ..IOR\Subjects\SQ1\Static 1\Main.any using the AnyBody software.
   2. In the AnyBody software, press 'Load' or F7 to load the model.
   3. Then, select 'RunParameterIdentification' next to it and press 'Execute.' This process may take approximately 5 minutes to fit the model and markers.
   4. Afterwards, select 'RunAnalysis' and press 'Execute.' This will perform inverse kinematics, inverse dynamics, and save the file.
   5. To process your dynamic trials, navigate to ..\IOR\Subjects\SQ1\IOR FB 1\Main.any, press 'Load,' choose 'RunAnalysis,' and then execute.
   6. Your dynamic trial will be processed, and you can review your data in 'Model View 1 => Chart 1.'
   7. Follow the same processing procedure for other dynamic trials as described in step 5 and 6.



### Loading issues
If the model fails to load, you may need to manually specify the installation path with of the model example and modify the script accordingly.
1. During the installation of AnyBody Modeling, make a note of where the \Documents\AnyBody.7.4.x\AMMR.v2.4.4-Demo folder is located, or find it manually.
2. Copy the path. In this case, it is C:\Users\UserName\Documents\AnyBody.7.4.x\AMMR.v2.4.4-Demo.
3. Open the generated IOR project and locate the libdef.any file.
4. Open it with a text editor, such as Notepad, and replace this line of code:
#ifndef LIBDEF_ANY
#include "<ANYBODY_PATH_INSTALLDIR>/AMMR/libdef.any"
#endif

with your path. In this example, it would be: #include "C:\Users\UserName\Documents\AnyBody.7.4.x/AMMR.v2.4.4-Demo/libdef.any" 
5. You may also need to modify the main.any files in ..\IOR\Subjects\SQ1\ by replacing the code above with:
#include "../../../libdef.any"


### Video tutorial
Video tutorial located in the repository.

## Resources for using the Qualisys Project Automation Framework (PAF)

The purpose of the ***Project Automation Framework*** (PAF) is to streamline the motion capture process from data collection to the final report. This repository contains an example project that illustrate how PAF can be used to implement custom automated data collection in [Qualisys Track Manager (QTM)](http://www.qualisys.com/software/qualisys-track-manager/), and how QTM can be connected to a processing engine. 

### PAF Documentation

The full documentation for PAF development is available here: [PAF Documentation](https://github.com/qualisys/paf-documentation).


### PAF Examples

Our official examples for various processing engines:

- [AnyBody](https://github.com/qualisys/paf-anybody-example)
- [Excel](https://github.com/qualisys/paf-excel-example)
- [Matlab](https://github.com/qualisys/paf-matlab-example)
- [OpenSim](https://github.com/qualisys/paf-opensim-example)
- [Python](https://github.com/qualisys/paf-python-example)
- [Theia Markerless](https://github.com/qualisys/paf-theia-markerless-example)
- [Theia Markerless Comparison](https://github.com/qualisys/paf-theia-markerless-comparison-example)
- [Theia Markerless True Hybrid](https://github.com/qualisys/paf-theia-markerless-true-hybrid-example)
- [Visual3D](https://github.com/qualisys/paf-visual3d-example)


_As of QTM version 2.17, the official Qualisys PAF examples can be used without any additional license. Note that some more advanced analysis types require a license for the "PAF Framework Developer kit" (Article number 150300)._
