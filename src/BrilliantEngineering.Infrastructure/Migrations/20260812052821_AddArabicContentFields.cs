using Microsoft.EntityFrameworkCore.Migrations;

#nullable disable

namespace BrilliantEngineering.Infrastructure.Migrations
{
    /// <inheritdoc />
    public partial class AddArabicContentFields : Migration
    {
        /// <inheritdoc />
        protected override void Up(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.AddColumn<string>(
                name: "JobTitleAr",
                table: "TeamMembers",
                type: "nvarchar(max)",
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "NameAr",
                table: "TeamMembers",
                type: "nvarchar(max)",
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "DescriptionAr",
                table: "Services",
                type: "nvarchar(max)",
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "TitleAr",
                table: "Services",
                type: "nvarchar(max)",
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "DescriptionAr",
                table: "ServiceCategories",
                type: "nvarchar(max)",
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "NameAr",
                table: "ServiceCategories",
                type: "nvarchar(max)",
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "NameAr",
                table: "ProjectTypes",
                type: "nvarchar(max)",
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "ClientNameAr",
                table: "Projects",
                type: "nvarchar(max)",
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "DescriptionAr",
                table: "Projects",
                type: "nvarchar(max)",
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "TitleAr",
                table: "Projects",
                type: "nvarchar(max)",
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "DescriptionAr",
                table: "Products",
                type: "nvarchar(max)",
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "NameAr",
                table: "Products",
                type: "nvarchar(max)",
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "NameAr",
                table: "ProductCategories",
                type: "nvarchar(max)",
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "DescriptionAr",
                table: "ProductBrands",
                type: "nvarchar(max)",
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "NameAr",
                table: "ProductBrands",
                type: "nvarchar(max)",
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "LabelAr",
                table: "HeroStats",
                type: "nvarchar(max)",
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "HeadlineBottomAr",
                table: "HeroSections",
                type: "nvarchar(max)",
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "HeadlineTopAr",
                table: "HeroSections",
                type: "nvarchar(max)",
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "PrimaryBtnTextAr",
                table: "HeroSections",
                type: "nvarchar(max)",
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "SecondaryBtnTextAr",
                table: "HeroSections",
                type: "nvarchar(max)",
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "SubTextAr",
                table: "HeroSections",
                type: "nvarchar(max)",
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "AddressAr",
                table: "ContactInfos",
                type: "nvarchar(max)",
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "NameAr",
                table: "Clients",
                type: "nvarchar(max)",
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "ContentAr",
                table: "BlogPosts",
                type: "nvarchar(max)",
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "MetaDescriptionAr",
                table: "BlogPosts",
                type: "nvarchar(max)",
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "MetaTitleAr",
                table: "BlogPosts",
                type: "nvarchar(max)",
                nullable: true);

            migrationBuilder.AddColumn<string>(
                name: "TitleAr",
                table: "BlogPosts",
                type: "nvarchar(max)",
                nullable: true);
        }

        /// <inheritdoc />
        protected override void Down(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropColumn(
                name: "JobTitleAr",
                table: "TeamMembers");

            migrationBuilder.DropColumn(
                name: "NameAr",
                table: "TeamMembers");

            migrationBuilder.DropColumn(
                name: "DescriptionAr",
                table: "Services");

            migrationBuilder.DropColumn(
                name: "TitleAr",
                table: "Services");

            migrationBuilder.DropColumn(
                name: "DescriptionAr",
                table: "ServiceCategories");

            migrationBuilder.DropColumn(
                name: "NameAr",
                table: "ServiceCategories");

            migrationBuilder.DropColumn(
                name: "NameAr",
                table: "ProjectTypes");

            migrationBuilder.DropColumn(
                name: "ClientNameAr",
                table: "Projects");

            migrationBuilder.DropColumn(
                name: "DescriptionAr",
                table: "Projects");

            migrationBuilder.DropColumn(
                name: "TitleAr",
                table: "Projects");

            migrationBuilder.DropColumn(
                name: "DescriptionAr",
                table: "Products");

            migrationBuilder.DropColumn(
                name: "NameAr",
                table: "Products");

            migrationBuilder.DropColumn(
                name: "NameAr",
                table: "ProductCategories");

            migrationBuilder.DropColumn(
                name: "DescriptionAr",
                table: "ProductBrands");

            migrationBuilder.DropColumn(
                name: "NameAr",
                table: "ProductBrands");

            migrationBuilder.DropColumn(
                name: "LabelAr",
                table: "HeroStats");

            migrationBuilder.DropColumn(
                name: "HeadlineBottomAr",
                table: "HeroSections");

            migrationBuilder.DropColumn(
                name: "HeadlineTopAr",
                table: "HeroSections");

            migrationBuilder.DropColumn(
                name: "PrimaryBtnTextAr",
                table: "HeroSections");

            migrationBuilder.DropColumn(
                name: "SecondaryBtnTextAr",
                table: "HeroSections");

            migrationBuilder.DropColumn(
                name: "SubTextAr",
                table: "HeroSections");

            migrationBuilder.DropColumn(
                name: "AddressAr",
                table: "ContactInfos");

            migrationBuilder.DropColumn(
                name: "NameAr",
                table: "Clients");

            migrationBuilder.DropColumn(
                name: "ContentAr",
                table: "BlogPosts");

            migrationBuilder.DropColumn(
                name: "MetaDescriptionAr",
                table: "BlogPosts");

            migrationBuilder.DropColumn(
                name: "MetaTitleAr",
                table: "BlogPosts");

            migrationBuilder.DropColumn(
                name: "TitleAr",
                table: "BlogPosts");
        }
    }
}
